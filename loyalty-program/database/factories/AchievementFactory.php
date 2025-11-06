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
            'achievement_type' => $this->faker->randomElement(AchievementType::cases()),
            'unlocked_at' => $this->faker->dateTime(),
            'metadata' => json_encode([
                'milestone' => $this->faker->sentence(),
            ]),
        ];
    }
}
