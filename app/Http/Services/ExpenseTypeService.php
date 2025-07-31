<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\Models\ExpenseType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ExpenseTypeService
{
    public function storeIconImage(UploadedFile $file, ExpenseType $expenseType): void
    {
        if ($expenseType->icon != null) {
            Storage::disk('public')->delete($expenseType->icon);
        }

        $file_name = time() . '.' . $file->getClientOriginalExtension();

        Storage::disk('public')->putFileAs("uploads/expense-types/{$expenseType->id}", $file, $file_name);

        $expenseType->fill([
            'icon' => '/uploads/expense-types/' . $expenseType->id . '/' . $file_name,
        ])->save();
    }
}
