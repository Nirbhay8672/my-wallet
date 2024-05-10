<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Website;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        Payment::truncate();
    }
}