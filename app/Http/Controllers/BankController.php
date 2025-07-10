<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BankController extends Controller
{
    public function index(): Response
    {
        $banks = Bank::all();
        return Inertia::render('banks/Index', [
            'banks' => $banks,
            'page_name' => 'Banks',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'required|string|max:255',
        ]);
        Bank::create($validated);
        return redirect()->route('banks.index')->with('success', 'Bank created successfully.');
    }

    public function update(Request $request, Bank $bank)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'required|string|max:255',
        ]);
        $bank->update($validated);
        return redirect()->route('banks.index')->with('success', 'Bank updated successfully.');
    }

    public function destroy(Bank $bank)
    {
        $bank->delete();
        return redirect()->route('banks.index')->with('success', 'Bank deleted successfully.');
    }
} 