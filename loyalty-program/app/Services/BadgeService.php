<?php

namespace App\Services;

use App\DTOs\UserDto;
use App\Enums\BadgeType;
use App\Events\BadgeUnlocked;
use App\Models\Achievement;
use App\Models\Badge;

class BadgeService
{
    public function checkAndUnlockBadges(UserDto $user): void
    {
        // Get total spending
        $totalSpent = \App\Models\LoyaltyTransaction::query()
            ->where('user_id', $user->id)
            ->where('type', 'purchase')
            ->sum('amount');

        // Get total achievements
        $achievementCount = Achievement::query()
            ->where('user_id', $user->id)
            ->count();

        // Check spender badges first
        $spenderBadges = [
            BadgeType::BRONZE_SPENDER->value => 100.00,
            BadgeType::SILVER_SPENDER->value => 1000.00,
            BadgeType::GOLD_SPENDER->value => 10000.00,
        ];

        foreach ($spenderBadges as $badgeTypeValue => $requiredAmount) {
            if ($totalSpent >= $requiredAmount) {
                $badgeType = BadgeType::from($badgeTypeValue);
                // If user doesn't already have this badge, award it
                $exists = Badge::query()
                    ->where('user_id', $user->id)
                    ->where('badge_type', $badgeType)
                    ->exists();
                if (! $exists) {
                    $badge = Badge::create([
                        'user_id' => $user->id,
                        'badge_type' => $badgeType,
                        'level' => $badgeType->getDefaultLevel(),
                        'earned_at' => now(),
                    ]);
                    event(new BadgeUnlocked($user, $badge));
                }
            }
        }

        // Now check achievement-based badges
        $achievementBadges = [
            BadgeType::LOYAL_CUSTOMER->value => 15,
            BadgeType::VIP_MEMBER->value => 50,
        ];

        foreach ($achievementBadges as $badgeTypeValue => $required) {
            if ($achievementCount >= $required) {
                $badgeType = BadgeType::from($badgeTypeValue);
                // If user doesn't already have this badge, award it
                $exists = Badge::query()
                    ->where('user_id', $user->id)
                    ->where('badge_type', $badgeType)
                    ->exists();

                if (! $exists) {
                    $badge = Badge::create([
                        'user_id' => $user->id,
                        'badge_type' => $badgeType,
                        'level' => $badgeType->getDefaultLevel(),
                        'earned_at' => now(),
                    ]);

                    event(new BadgeUnlocked($user, $badge));
                }
            }
        }
    }

    public function getUserBadges(UserDto $user): array
    {
        $badgeStats = Badge::query()
            ->where('user_id', $user->id)
            ->selectRaw('
        COUNT(*) as total_earned,
        MAX(level) as highest_level,
        JSON_ARRAYAGG(
          JSON_OBJECT(
            "id", id,
            "badge_type", badge_type,
            "level", level,
            "earned_at", earned_at
          )
        ) as badge_details
      ')
            ->first();

        $badges = json_decode($badgeStats->badge_details ?? '[]', true);

        return [
            'badges' => $badges,
            'total_earned' => $badgeStats->total_earned ?? 0,
            'highest_level' => $badgeStats->highest_level,
        ];
    }
}
