<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Seeder;

class CashAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting to add Cash accounts for all users...');

        // Get all users
        $users = User::all();
        $createdCount = 0;
        $skippedCount = 0;

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

                $createdCount++;
                $this->command->info("✓ Created Cash account for user: {$user->name} ({$user->email})");
            } else {
                $skippedCount++;
                $this->command->info("- Skipped: Cash account already exists for user: {$user->name} ({$user->email})");
            }
        }

        $this->command->info('');
        $this->command->info("Cash Account Seeding Summary:");
        $this->command->info("✓ Created: {$createdCount} new Cash accounts");
        $this->command->info("- Skipped: {$skippedCount} existing Cash accounts");
        $this->command->info("✓ Total users processed: " . $users->count());
        $this->command->info('Cash account seeding completed successfully!');
    }
}
