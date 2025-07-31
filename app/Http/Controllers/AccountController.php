<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function index(Request $request): Response
    {
        $accounts = Auth::user()->accounts()->with('bank')->get();
        $banks = Bank::all();
        return Inertia::render('accounts/Index', [
            'accounts' => $accounts,
            'banks' => $banks,
            'page_name' => 'Accounts',
            'auth' => [
                'user' => Auth::user()->load(['roles.permissions'])
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'initial_amount' => 'required|numeric',
            'bank_id' => 'nullable|exists:banks,id',
            'account_number' => 'nullable|string|max:255',
        ]);
        $validated['type'] = $validated['bank_id'] ? 'bank' : 'cash';
        $validated['balance'] = $validated['initial_amount'];
        $validated['user_id'] = Auth::id();
        $account = Account::create($validated);
        return $this->successResponse(message: "{$account->name} account has been created successfully.");
    }

    public function update(Request $request, Account $account)
    {
        if ($account->type === 'cash') {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'initial_amount' => 'required|numeric',
            ]);
            $account->update([
                'name' => $validated['name'],
                'initial_amount' => $validated['initial_amount'],
                'balance' => $validated['initial_amount'],
            ]);
        } else {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'initial_amount' => 'required|numeric',
                'bank_id' => 'required|exists:banks,id',
                'account_number' => 'nullable|string|max:255',
            ]);
            $account->update($validated);
        }
        return $this->successResponse(message: "{$account->name} account has been updated successfully.");
    }

    public function destroy(Account $account)
    {
        if ($account->type === 'cash') {
            return redirect()->route('accounts.index')->with('error', 'Cash account cannot be deleted.');
        }
        $account->delete();
        return redirect()->route('accounts.index')->with('success', 'Account deleted successfully.');
    }
}
