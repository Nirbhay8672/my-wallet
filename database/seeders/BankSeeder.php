<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class BankSeeder extends Seeder
{
    public function run(): void
    {
        $banks = [
            [
                'name' => 'HDFC Bank',
                'image_file' => 'HDFC.jpeg',
                'active' => true,
            ],
            [
                'name' => 'State Bank of India',
                'image_file' => 'SBI.jpeg',
                'active' => true,
            ],
            [
                'name' => 'Punjab National Bank',
                'image_file' => 'PNB.jpeg',
                'active' => true,
            ],
            [
                'name' => 'Axis Bank',
                'image_file' => 'AXIS.jpeg',
                'active' => true,
            ],
        ];

        foreach ($banks as $bankData) {
            $bank = Bank::create([
                'name' => $bankData['name'],
                'logo' => 'temp', // Will be updated after storing the image
                'active' => $bankData['active'],
            ]);

            $this->storeBankLogo($bank, $bankData['image_file']);
        }
    }

    private function storeBankLogo(Bank $bank, string $imageFileName): void
    {
        $sourceFilePath = public_path("images/banks/{$imageFileName}");
        $destinationPath = public_path("uploads/banks/{$bank->id}");
        $fileName = time() . '_' . $imageFileName;

        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0777, true, true);
        }

        if (File::exists($sourceFilePath)) {
            File::copy($sourceFilePath, $destinationPath . '/' . $fileName);

            $bank->update([
                'logo' => "/uploads/banks/{$bank->id}/{$fileName}",
            ]);
        }
    }
}
