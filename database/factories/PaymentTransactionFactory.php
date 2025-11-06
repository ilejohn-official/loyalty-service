<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\PaymentTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentTransactionFactory extends Factory
{
    protected $model = PaymentTransaction::class;

    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'amount' => fake()->randomFloat(2, 1, 10000),
            'provider_reference' => fake()->uuid(),
            'status' => fake()->randomElement(PaymentStatus::cases()),
        ];
    }
}
