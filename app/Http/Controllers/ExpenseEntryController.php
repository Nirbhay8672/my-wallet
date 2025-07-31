<?php

namespace App\Http\Controllers;

use App\Models\ExpenseEntry;
use App\Models\Account;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseEntryController extends Controller
{
    public function index(Request $request): Response
    {
        $accountId = $request->get('account_id');
        $type = $request->get('type', 'expense');
        $expenseTypeId = $request->get('expense_type_id');
        $dateRange = $request->get('date_range');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = ExpenseEntry::with(['account', 'expenseType', 'user'])
            ->where('user_id', Auth::id());

        if ($accountId) {
            $query->where('account_id', $accountId);
        }

        if ($expenseTypeId) {
            $query->where('expense_type_id', $expenseTypeId);
        }

        // Date filtering
        if ($dateRange) {
            switch ($dateRange) {
                case 'this_week':
                    $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]);
                    break;
                case 'this_year':
                    $query->whereBetween('date', [now()->startOfYear(), now()->endOfYear()]);
                    break;
            }
        } elseif ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        $entries = $query->orderBy('created_at', 'desc')->paginate(15);

        $accounts = Account::where('user_id', Auth::id())->get();
        $expenseTypes = ExpenseType::where('active', true)->get();

        return Inertia::render('expense-entries/Index', [
            'entries' => $entries,
            'accounts' => $accounts,
            'expenseTypes' => $expenseTypes,
            'page_name' => 'Expenses',
            'filters' => [
                'account_id' => $accountId,
                'type' => $type,
                'expense_type_id' => $expenseTypeId,
                'date_range' => $dateRange,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]
        ]);
    }

    public function create(Request $request)
    {
        $accountId = $request->get('account_id');
        $accounts = Account::where('user_id', Auth::id())->get();
        $expenseTypes = ExpenseType::where('active', true)->get();

        return Inertia::render('expense-entries/Form', [
            'accounts' => $accounts,
            'expenseTypes' => $expenseTypes,
            'accountId' => $accountId,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'account_id' => 'required|exists:accounts,id',
            'expense_type_id' => 'required|exists:expense_types,id',
            'amount' => 'required|numeric|min:0.01|gt:0',
            'proof' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'description' => 'nullable|string',
            'date' => 'required|date|before_or_equal:today',
            'time' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['user_id'] = Auth::id();

            // Handle file upload
            if ($request->hasFile('proof')) {
                $file = $request->file('proof');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/expense_proofs', $fileName);
                $data['proof'] = 'storage/expense_proofs/' . $fileName;
            }

            $expenseEntry = ExpenseEntry::create($data);

            // Update account balance (subtract for expense)
            $account = Account::find($request->account_id);
            $account->balance -= $request->amount;
            $account->save();

            DB::commit();

            return $this->successResponse(message: "Expense entry has been added successfully.");
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorResponse(message: "Failed to add expense entry: " . $e->getMessage());
        }
    }

    public function show(ExpenseEntry $expenseEntry)
    {
        $expenseEntry->load(['account', 'expenseType', 'user']);
        return Inertia::render('expense-entries/Show', [
            'entry' => $expenseEntry
        ]);
    }

    public function edit(ExpenseEntry $expenseEntry)
    {
        $accounts = Account::where('user_id', Auth::id())->get();
        $expenseTypes = ExpenseType::where('active', true)->get();

        return Inertia::render('expense-entries/Form', [
            'entry' => $expenseEntry,
            'accounts' => $accounts,
            'expenseTypes' => $expenseTypes,
        ]);
    }

    public function update(Request $request, ExpenseEntry $expenseEntry)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'account_id' => 'required|exists:accounts,id',
            'expense_type_id' => 'required|exists:expense_types,id',
            'amount' => 'required|numeric|min:0.01|gt:0',
            'proof' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'description' => 'nullable|string',
            'date' => 'required|date|before_or_equal:today',
            'time' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $oldAmount = $expenseEntry->amount;
            $newAmount = $request->amount;
            $amountDifference = $newAmount - $oldAmount;

            $data = $request->all();

            // Handle file upload
            if ($request->hasFile('proof')) {
                $file = $request->file('proof');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/expense_proofs', $fileName);
                $data['proof'] = 'storage/expense_proofs/' . $fileName;
            }

            $expenseEntry->update($data);

            // Update account balance based on amount difference (subtract for expense)
            if ($amountDifference != 0) {
                $account = Account::find($request->account_id);
                $account->balance -= $amountDifference;
                $account->save();
            }

            DB::commit();

            return $this->successResponse(message: "Expense entry has been updated successfully.");
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorResponse(message: "Failed to update expense entry: " . $e->getMessage());
        }
    }

    public function destroy(ExpenseEntry $expenseEntry)
    {
        DB::beginTransaction();
        try {
            $amount = $expenseEntry->amount;
            $account = $expenseEntry->account;

            $expenseEntry->delete();

            // Add back to account balance (reverse the expense)
            $account->balance += $amount;
            $account->save();

            DB::commit();

            return $this->successResponse(message: "Expense entry has been deleted successfully.");
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorResponse(message: "Failed to delete expense entry: " . $e->getMessage());
        }
    }
}
