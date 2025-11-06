<?php

namespace Database\Factories;

use App\Enums\AchievementType;
use App\Models\Achievement;
use Illuminate\Database\Eloquent\Factories\Factory;

class AchievementFactory extends Factory
{
    protected $model = Achievement::class;

    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'achievement_type' => fake()->randomElement(AchievementType::cases()),
            'unlocked_at' => fake()->dateTime(),
            'metadata' => json_encode([
                'milestone' => fake()->sentence(),
            ]),
        ];
    }
}
