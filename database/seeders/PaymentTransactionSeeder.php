<?php

namespace Database\Seeders;

use App\Models\PaymentTransaction;
use App\Enums\PaymentStatus;
use Illuminate\Database\Seeder;

class PaymentTransactionSeeder extends Seeder
{
  public function run(): void
  {
    // Create 50 payment transactions for testing
    // Using array of 10 user IDs to simulate multiple transactions per user
    $userIds = range(1, 10);

    foreach ($userIds as $userId) {
      // Create 5 transactions per user
      for ($i = 0; $i < 5; $i++) {
        PaymentTransaction::create([
          'user_id' => $userId,
          'amount' => fake()->randomFloat(2, 10, 1000),
          'provider_reference' => fake()->unique()->uuid(),
          'status' => fake()->randomElement(PaymentStatus::cases()),
          'created_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ]);
      }
    }
  }
}
