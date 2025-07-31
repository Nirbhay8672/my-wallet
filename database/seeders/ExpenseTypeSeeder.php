<?php

namespace Database\Seeders;

use App\Models\ExpenseType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ExpenseTypeSeeder extends Seeder
{
    public function run(): void
    {
        $expenseTypes = [
            [
                'name' => 'Food & Dining',
                'icon' => '/images/expense-types/food.png',
                'active' => true,
            ],
            [
                'name' => 'Transportation',
                'icon' => '/images/expense-types/transport.png',
                'active' => true,
            ],
            [
                'name' => 'Shopping',
                'icon' => '/images/expense-types/shopping.png',
                'active' => true,
            ],
            [
                'name' => 'Bills & Utilities',
                'icon' => '/images/expense-types/bills.png',
                'active' => true,
            ],
            [
                'name' => 'Healthcare',
                'icon' => '/images/expense-types/healthcare.png',
                'active' => true,
            ],
            [
                'name' => 'Entertainment',
                'icon' => '/images/expense-types/entertainment.png',
                'active' => true,
            ],
            [
                'name' => 'Education',
                'icon' => '/images/expense-types/education.png',
                'active' => true,
            ],
            [
                'name' => 'Other',
                'icon' => '/images/expense-types/other.png',
                'active' => true,
            ],
        ];

        foreach ($expenseTypes as $expenseTypeData) {
            ExpenseType::create($expenseTypeData);
        }
    }
}
