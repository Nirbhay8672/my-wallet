<?php

namespace Database\Seeders;

use App\Models\IncomeType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class IncomeTypeSeeder extends Seeder
{
    public function run(): void
    {
        $incomeTypes = [
            [
                'name' => 'Salary',
                'icon' => '/images/income-types/salary.png',
                'active' => true,
            ],
            [
                'name' => 'Freelance',
                'icon' => '/images/income-types/freelance.png',
                'active' => true,
            ],
            [
                'name' => 'Business',
                'icon' => '/images/income-types/business.png',
                'active' => true,
            ],
            [
                'name' => 'Investment',
                'icon' => '/images/income-types/investment.png',
                'active' => true,
            ],
            [
                'name' => 'Rental',
                'icon' => '/images/income-types/rental.png',
                'active' => true,
            ],
            [
                'name' => 'Bonus',
                'icon' => '/images/income-types/bonus.png',
                'active' => true,
            ],
            [
                'name' => 'Other',
                'icon' => '/images/income-types/other.png',
                'active' => true,
            ],
        ];

        foreach ($incomeTypes as $incomeTypeData) {
            IncomeType::create($incomeTypeData);
        }
    }
}
