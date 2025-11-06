<?php

namespace Database\Factories;

use App\Enums\TransactionType;
use App\Models\LoyaltyTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoyaltyTransactionFactory extends Factory
{
    protected $model = LoyaltyTransaction::class;

    public function definition(): array
    {
        $amount = $this->faker->randomFloat(2, 1, 10000);
        $type = $this->faker->randomElement(TransactionType::cases());

        return [
            'user_id' => \App\Models\User::factory(),
            'amount' => $amount,
            'type' => $type,
            // Basic points calculation: 1 point per 10 units of currency (integer)
            'points_earned' => (int) floor($amount / 10),
            'reference' => $this->faker->uuid(),
        ];
    }
}
