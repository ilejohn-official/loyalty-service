<?php

namespace Database\Seeders;

use Faker\Generator;
use App\Models\Achievement;
use App\Enums\AchievementType;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $faker = app(Generator::class);
        $userIds = range(1, 10);
        $achievementTypes = AchievementType::cases();
        foreach ($userIds as $userId) {
            // Randomly unlock some achievements for each user
            $unlockedTypes = $faker->randomElements(
                $achievementTypes,
                $faker->numberBetween(0, count($achievementTypes))
            );

            foreach ($unlockedTypes as $type) {
                Achievement::factory()->create([
                    'user_id' => $userId,
                    'achievement_type' => $type,
                    'metadata' => json_encode([
                        'milestone' => $type->getMilestone(),
                    ]),
                ]);
            }
        }
    }
}
