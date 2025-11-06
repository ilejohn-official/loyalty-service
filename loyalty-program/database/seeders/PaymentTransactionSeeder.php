<?php

namespace Database\Seeders;

use App\Enums\PaymentStatus;
use Illuminate\Database\Seeder;
use App\Models\PaymentTransaction;

class PaymentTransactionSeeder extends Seeder
{
    public function run(): void
    {
        // Create 50 payment transactions for testing
        // Using array of 10 user IDs to simulate multiple transactions per user
        $userIds = range(1, 10);

        foreach ($userIds as $userId) {
            // Create 5 transactions per user
            PaymentTransaction::factory()->count(5)->create(['user_id' => $userId]);
        }
    }
}
