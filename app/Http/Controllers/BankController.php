<?php

namespace App\Http\Controllers;

use App\Http\Requests\BankFormRequest;
use App\Http\Services\BankService;
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

    public function store(BankFormRequest $request, BankService $bankService)
    {
        $bank = Bank::create($request->fields());

        if ($request->logo) {
            $bankService->storeLogoImage($request->file('logo'), $bank);
        }

        return $this->successResponse(message: "{$bank->name} has been {$request->action()} successfully.");
    }

    public function update(BankFormRequest $request, Bank $bank, BankService $bankService)
    {
        $bank->update([
            'name' => $request->validated('name'),
            'active' => $request->boolean('active', true),
        ]);

        if ($request->hasFile('logo')) {
            $bankService->storeLogoImage($request->file('logo'), $bank);
        }

        return $this->successResponse(message: "{$bank->name} has been updated successfully.");
    }

    public function destroy(Bank $bank)
    {
        $bank->delete();
        return redirect()->route('banks.index')->with('success', 'Bank deleted successfully.');
    }

    public function toggleStatus(Request $request, Bank $bank)
    {
        $bank->update([
            'active' => $request->input('active', !$bank->active)
        ]);

        $status = $bank->active ? 'activated' : 'deactivated';
        return $this->successResponse(message: "{$bank->name} has been {$status} successfully.");
    }
}
