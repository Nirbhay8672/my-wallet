<?php

namespace App\Http\Controllers;

use App\Http\Requests\IncomeTypeFormRequest;
use App\Http\Services\IncomeTypeService;
use App\Models\IncomeType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IncomeTypeController extends Controller
{
    public function index(): Response
    {
        $incomeTypes = IncomeType::all();
        return Inertia::render('income-types/Index', [
            'incomeTypes' => $incomeTypes,
            'page_name' => 'Income Types',
        ]);
    }

    public function store(IncomeTypeFormRequest $request, IncomeTypeService $incomeTypeService)
    {
        $incomeType = IncomeType::create($request->fields());

        if ($request->icon) {
            $incomeTypeService->storeIconImage($request->file('icon'), $incomeType);
        }

        return $this->successResponse(message: "{$incomeType->name} has been {$request->action()} successfully.");
    }

    public function update(IncomeTypeFormRequest $request, IncomeType $incomeType, IncomeTypeService $incomeTypeService)
    {
        $incomeType->update([
            'name' => $request->validated('name'),
            'active' => $request->boolean('active', true),
        ]);

        if ($request->hasFile('icon')) {
            $incomeTypeService->storeIconImage($request->file('icon'), $incomeType);
        }

        return $this->successResponse(message: "{$incomeType->name} has been updated successfully.");
    }

    public function destroy(IncomeType $incomeType)
    {
        $incomeType->delete();
        return redirect()->route('income-types.index')->with('success', 'Income Type deleted successfully.');
    }

    public function toggleStatus(Request $request, IncomeType $incomeType)
    {
        $incomeType->update([
            'active' => $request->input('active', !$incomeType->active)
        ]);

        $status = $incomeType->active ? 'activated' : 'deactivated';
        return $this->successResponse(message: "{$incomeType->name} has been {$status} successfully.");
    }
}
