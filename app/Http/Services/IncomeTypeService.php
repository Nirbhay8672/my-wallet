<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\Models\IncomeType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class IncomeTypeService
{
    public function storeIconImage(UploadedFile $file, IncomeType $incomeType): void
    {
        if ($incomeType->icon != null) {
            Storage::disk('public')->delete($incomeType->icon);
        }

        $file_name = time() . '.' . $file->getClientOriginalExtension();

        Storage::disk('public')->putFileAs("uploads/income-types/{$incomeType->id}", $file, $file_name);

        $incomeType->fill([
            'icon' => '/uploads/income-types/' . $incomeType->id . '/' . $file_name,
        ])->save();
    }
}
