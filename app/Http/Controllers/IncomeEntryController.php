<?php

namespace App\Http\Controllers;

use App\Models\IncomeEntry;
use App\Models\Account;
use App\Models\IncomeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class IncomeEntryController extends Controller
{
    public function index(Request $request): Response
    {
        $accountId = $request->get('account_id');
        $type = $request->get('type', 'income');
        $incomeTypeId = $request->get('income_type_id');
        $dateRange = $request->get('date_range');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = IncomeEntry::with(['account', 'incomeType', 'user'])
            ->where('user_id', Auth::id());

        if ($accountId) {
            $query->where('account_id', $accountId);
        }

        if ($incomeTypeId) {
            $query->where('income_type_id', $incomeTypeId);
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
        $incomeTypes = IncomeType::where('active', true)->get();

        return Inertia::render('income-entries/Index', [
            'entries' => $entries,
            'accounts' => $accounts,
            'page_name' => 'Incomes',
            'incomeTypes' => $incomeTypes,
            'filters' => [
                'account_id' => $accountId,
                'type' => $type,
                'income_type_id' => $incomeTypeId,
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
        $incomeTypes = IncomeType::where('active', true)->get();

        return Inertia::render('income-entries/Form', [
            'accounts' => $accounts,
            'incomeTypes' => $incomeTypes,
            'accountId' => $accountId,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'account_id' => 'required|exists:accounts,id',
            'income_type_id' => 'required|exists:income_types,id',
            'amount' => 'required|numeric|min:0.01|gt:0',
            'proof' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'required',
        ]);

        // Custom validation for date to allow today's date
        $selectedDate = \Carbon\Carbon::parse($request->date);
        $today = \Carbon\Carbon::today();

        if ($selectedDate->gt($today)) {
            return response()->json([
                'message' => 'Date cannot be in the future.',
                'errors' => [
                    'date' => ['Date cannot be in the future.']
                ]
            ], 422);
        }

        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['user_id'] = Auth::id();

            // Handle file upload
            if ($request->hasFile('proof')) {
                $file = $request->file('proof');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/income_proofs', $fileName);
                $data['proof'] = 'storage/income_proofs/' . $fileName;
            }

            $incomeEntry = IncomeEntry::create($data);

            // Update account balance
            $account = Account::find($request->account_id);
            $account->balance += $request->amount;
            $account->save();

            DB::commit();

            return $this->successResponse(message: "Income entry has been added successfully.");
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorResponse(message: "Failed to add income entry: " . $e->getMessage());
        }
    }

    public function show(IncomeEntry $incomeEntry)
    {
        $incomeEntry->load(['account', 'incomeType', 'user']);
        return Inertia::render('income-entries/Show', [
            'entry' => $incomeEntry
        ]);
    }

    public function edit(IncomeEntry $incomeEntry)
    {
        $accounts = Account::where('user_id', Auth::id())->get();
        $incomeTypes = IncomeType::where('active', true)->get();

        return Inertia::render('income-entries/Form', [
            'entry' => $incomeEntry,
            'accounts' => $accounts,
            'incomeTypes' => $incomeTypes,
        ]);
    }

    public function update(Request $request, IncomeEntry $incomeEntry)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'account_id' => 'required|exists:accounts,id',
            'income_type_id' => 'required|exists:income_types,id',
            'amount' => 'required|numeric|min:0.01|gt:0',
            'proof' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'required',
        ]);

        // Custom validation for date to allow today's date
        $selectedDate = \Carbon\Carbon::parse($request->date);
        $today = \Carbon\Carbon::today();

        if ($selectedDate->gt($today)) {
            return response()->json([
                'message' => 'Date cannot be in the future.',
                'errors' => [
                    'date' => ['Date cannot be in the future.']
                ]
            ], 422);
        }

        DB::beginTransaction();
        try {
            $oldAmount = $incomeEntry->amount;
            $newAmount = $request->amount;
            $amountDifference = $newAmount - $oldAmount;

            $data = $request->all();

            // Handle file upload
            if ($request->hasFile('proof')) {
                $file = $request->file('proof');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/income_proofs', $fileName);
                $data['proof'] = 'storage/income_proofs/' . $fileName;
            }

            $incomeEntry->update($data);

            // Update account balance based on amount difference
            if ($amountDifference != 0) {
                $account = Account::find($request->account_id);
                $account->balance += $amountDifference;
                $account->save();
            }

            DB::commit();

            return $this->successResponse(message: "Income entry has been updated successfully.");
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorResponse(message: "Failed to update income entry: " . $e->getMessage());
        }
    }

    public function destroy(IncomeEntry $incomeEntry)
    {
        DB::beginTransaction();
        try {
            $amount = $incomeEntry->amount;
            $account = $incomeEntry->account;

            $incomeEntry->delete();

            // Subtract from account balance
            $account->balance -= $amount;
            $account->save();

            DB::commit();

            return $this->successResponse(message: "Income entry has been deleted successfully.");
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorResponse(message: "Failed to delete income entry: " . $e->getMessage());
        }
    }
}
