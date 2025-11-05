<?php

namespace Database\Seeders;

use App\Models\AchievementProgress;
use App\Enums\AchievementType;
use Illuminate\Database\Seeder;

class AchievementProgressSeeder extends Seeder
{
  public function run(): void
  {
    $userIds = range(1, 10);
    $achievementTypes = AchievementType::cases();
    foreach ($userIds as $userId) {
      foreach ($achievementTypes as $type) {
        $targetValue = $type->getTargetValue();        // Randomly set some progress towards achievements
        $currentValue = fake()->randomFloat(2, 0, $targetValue * 1.2);

        AchievementProgress::create([
          'user_id' => $userId,
          'achievement_type' => $type,
          'current_value' => $currentValue,
          'target_value' => $targetValue,
        ]);
      }
    }
  }
}
