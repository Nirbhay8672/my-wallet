<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\Models\Bank;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BankService
{
    public function storeLogoImage(UploadedFile $file, Bank $bank): void
    {
        if ($bank->logo != null) {
            Storage::disk('public')->delete($bank->logo);
        }

        $file_name = time() . '.' . $file->getClientOriginalExtension();

        Storage::disk('public')->putFileAs("uploads/banks/{$bank->id}", $file, $file_name);

        $bank->fill([
            'logo' => '/uploads/banks/' . $bank->id . '/' . $file_name,
        ])->save();
    }
}
