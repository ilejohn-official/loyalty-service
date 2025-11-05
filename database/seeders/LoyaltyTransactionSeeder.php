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
      // Create multiple loyalty point entries per user
      for ($i = 0; $i < 5; $i++) {
        $points = fake()->randomElement([10, 20, 50, 100]);
        $types = ['purchase', 'bonus', 'referral'];

        LoyaltyTransaction::create([
          'user_id' => $userId,
          'points' => $points,
          'type' => fake()->randomElement($types),
          'metadata' => json_encode([
            'source' => fake()->randomElement(['online', 'in-store', 'mobile-app']),
            'description' => fake()->sentence(),
          ]),
          'created_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ]);
      }
    }
  }
}
