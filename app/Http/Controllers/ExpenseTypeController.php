<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseTypeFormRequest;
use App\Http\Services\ExpenseTypeService;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseTypeController extends Controller
{
    public function index(): Response
    {
        $expenseTypes = ExpenseType::all();
        return Inertia::render('expense-types/Index', [
            'expenseTypes' => $expenseTypes,
            'page_name' => 'Expense Types',
        ]);
    }

    public function store(ExpenseTypeFormRequest $request, ExpenseTypeService $expenseTypeService)
    {
        $expenseType = ExpenseType::create($request->fields());

        if ($request->icon) {
            $expenseTypeService->storeIconImage($request->file('icon'), $expenseType);
        }

        return $this->successResponse(message: "{$expenseType->name} has been {$request->action()} successfully.");
    }

    public function update(ExpenseTypeFormRequest $request, ExpenseType $expenseType, ExpenseTypeService $expenseTypeService)
    {
        $expenseType->update([
            'name' => $request->validated('name'),
            'active' => $request->boolean('active', true),
        ]);

        if ($request->hasFile('icon')) {
            $expenseTypeService->storeIconImage($request->file('icon'), $expenseType);
        }

        return $this->successResponse(message: "{$expenseType->name} has been updated successfully.");
    }

    public function destroy(ExpenseType $expenseType)
    {
        $expenseType->delete();
        return redirect()->route('expense-types.index')->with('success', 'Expense Type deleted successfully.');
    }

    public function toggleStatus(Request $request, ExpenseType $expenseType)
    {
        $expenseType->update([
            'active' => $request->input('active', !$expenseType->active)
        ]);

        $status = $expenseType->active ? 'activated' : 'deactivated';
        return $this->successResponse(message: "{$expenseType->name} has been {$status} successfully.");
    }
}
