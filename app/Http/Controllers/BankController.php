<?php

namespace App\Http\Controllers;

use App\Http\Requests\BankFormRequest;
use App\Http\Services\BankService;
use App\Models\Bank;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Requests\BankFormRequest;
use Illuminate\Support\Facades\Storage;

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
        Storage::disk('public')->delete("/uploads/banks/{$bank->logo}");
        $bank->delete();

        return $this->successResponse(message: "{$bank->name} bank has been deleted successfully.");
    }

    public function datatable(Request $request)
    {
        try {
            $search = $request->search;
            $perPage = $request->per_page ?? 10;
            $page = $request->page ?? 1;

            $query = Bank::query();

            if ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            }

            $total = $query->count();
            $offset = ($page - 1) * $perPage;

            $banks = $query->offset($offset)
                ->limit($perPage)
                ->get();

            $total_pages = ceil($total / $perPage);

            $startIndex = ($page - 1) * $perPage;
            $endIndex = min($startIndex + $perPage, $total);

            return response()->json([
                'banks' => $banks,
                'total' => $total,
                'total_pages' => $total_pages,
                'start_index' => $startIndex + 1,
                'end_index' => $endIndex,
            ]);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }
}
