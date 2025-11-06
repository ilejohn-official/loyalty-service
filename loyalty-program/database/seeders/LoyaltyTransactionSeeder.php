<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LoyaltyTransaction;

class LoyaltyTransactionSeeder extends Seeder
{
    public function run(): void
    {
        // Create loyalty transactions based on payment transactions
        // Using same user IDs as payment transactions
        $userIds = range(1, 10);

        foreach ($userIds as $userId) {
            LoyaltyTransaction::factory()->count(5)->create([
                'user_id' => $userId,
            ]);
        }
    }
}
