<?php

namespace App\Services;

use App\DTOs\UserDto;
use App\Enums\AchievementType;
use App\Events\AchievementUnlocked;
use App\Models\Achievement;
use App\Models\AchievementProgress;

class AchievementService
{
    public function checkAndUnlockAchievements(UserDto $user, float $amount): void
    {
        // Update progress and check for unlocked achievements in a single transaction
        $progressAchievements = AchievementProgress::query()
            ->where('user_id', $user->id)
            ->selectRaw(
                '
        id,
        achievement_type,
        current_value,
        target_value,
        CASE 
          WHEN achievement_type IN (?, ?) THEN current_value + ?
          WHEN achievement_type IN (?, ?, ?) THEN current_value + 1
          ELSE current_value
        END as new_value',
                [
                    AchievementType::SPEND_AMOUNT_100->value,
                    AchievementType::SPEND_AMOUNT_1000->value,
                    $amount,
                    AchievementType::FIRST_PURCHASE->value,
                    AchievementType::PURCHASE_COUNT_5->value,
                    AchievementType::PURCHASE_COUNT_10->value,
                ]
            )
            ->get();

        foreach ($progressAchievements as $progress) {

            AchievementProgress::query()
                ->where('id', $progress->id)
                ->update(['current_value' => $progress->new_value]);

            // Refresh the progress object to get the actual updated value
            $updatedProgress = AchievementProgress::find($progress->id);

            // Check if achievement should be unlocked
            if ($updatedProgress->current_value >= $updatedProgress->target_value) {

                // Try to create achievement - will fail if exists due to unique constraint
                $achievement = Achievement::query()
                    ->firstOrCreate(
                        [
                            'user_id' => $user->id,
                            'achievement_type' => $updatedProgress->achievement_type,
                        ],
                        [
                            'unlocked_at' => now(),
                            'metadata' => json_encode([
                                'current_value' => $updatedProgress->current_value,
                                'target_value' => $updatedProgress->target_value,
                            ]),
                        ]
                    );

                if ($achievement->wasRecentlyCreated) {
                    event(new AchievementUnlocked($user, $achievement));
                }
            }
        }
    }

    public function getUserProgress(UserDto $user): array
    {
        $progress = AchievementProgress::query()
            ->where('user_id', $user->id)
            ->selectRaw('
        JSON_ARRAYAGG(
          JSON_OBJECT(
            "achievement_type", achievement_type,
            "current", current_value,
            "target", target_value
          )
        ) as progress_data
      ')
            ->first();

        $achievements = Achievement::query()
            ->where('user_id', $user->id)
            ->selectRaw('
        COUNT(*) as total_unlocked,
        JSON_ARRAYAGG(
          JSON_OBJECT(
            "id", id,
            "achievement_type", achievement_type,
            "unlocked_at", unlocked_at,
            "metadata", metadata
          )
        ) as unlocked_data
      ')
            ->first();

        return [
            'progress' => json_decode($progress->progress_data ?? '[]', true),
            'unlocked' => json_decode($achievements->unlocked_data ?? '[]', true),
            'total_unlocked' => $achievements->total_unlocked ?? 0,
        ];
    }
}
