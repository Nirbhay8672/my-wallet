<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();

        foreach ($users as $user) {
            // Check if user already has a cash account
            $existingCashAccount = Account::where('user_id', $user->id)
                ->where('type', 'cash')
                ->where('name', 'Cash')
                ->first();

            // Only create cash account if it doesn't exist
            if (!$existingCashAccount) {
                Account::create([
                    'user_id' => $user->id,
                    'name' => 'Cash',
                    'type' => 'cash',
                    'account_number' => null,
                    'initial_amount' => 0.00,
                    'balance' => 0.00,
                    'bank_id' => null, // Cash accounts don't have a bank
                ]);

                $this->command->info("Created Cash account for user: {$user->name} ({$user->email})");
            } else {
                $this->command->info("Cash account already exists for user: {$user->name} ({$user->email})");
            }
        }

        $this->command->info('Account seeding completed successfully!');
    }
}
