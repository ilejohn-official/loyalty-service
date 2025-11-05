<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Enums\AchievementType;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
  public function run(): void
  {
    $userIds = range(1, 10);
    $achievementTypes = AchievementType::cases();
    foreach ($userIds as $userId) {
      // Randomly unlock some achievements for each user
      $unlockedTypes = fake()->randomElements(
        $achievementTypes,
        fake()->numberBetween(0, count($achievementTypes))
      );

      foreach ($unlockedTypes as $type) {
        Achievement::create([
          'user_id' => $userId,
          'achievement_type' => $type,
          'unlocked_at' => fake()->dateTimeBetween('-2 months', 'now'),
          'metadata' => json_encode([
            'milestone' => $type->getMilestone(),
          ]),
        ]);
      }
    }
  }
}
