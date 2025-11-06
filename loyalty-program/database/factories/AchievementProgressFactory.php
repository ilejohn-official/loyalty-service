<?php

namespace Database\Factories;

use App\Enums\AchievementType;
use App\Models\AchievementProgress;
use Illuminate\Database\Eloquent\Factories\Factory;

class AchievementProgressFactory extends Factory
{
    protected $model = AchievementProgress::class;

    public function definition(): array
    {
        $type = fake()->randomElement(AchievementType::cases());
        $targetValue = $type->getTargetValue();

        return [
            'user_id' => \App\Models\User::factory(),
            'achievement_type' => $type,
            'current_value' => fake()->randomFloat(2, 0, $targetValue),
            'target_value' => $targetValue,
        ];
    }
}
