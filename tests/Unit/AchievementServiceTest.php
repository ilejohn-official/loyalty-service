<?php

use App\DTOs\UserDto;
use App\Enums\AchievementType;
use App\Models\Achievement;
use App\Models\AchievementProgress;

beforeEach(function () {
    $this->achievementService = app()->make(\App\Services\AchievementService::class);
});

test('checkAndUnlockAchievements unlocks achievement when progress meets target', function () {
    // Arrange
    $user = UserDto::fromModel($this->user);
    $achievementType = AchievementType::SPEND_AMOUNT_100;

    // Create progress record close to target
    AchievementProgress::factory()->create([
        'user_id' => $user->id,
        'achievement_type' => $achievementType,
        'current_value' => 90,
        'target_value' => 100,
    ]);

    // Act
    $this->achievementService->checkAndUnlockAchievements($user, 20.0);

    // Assert
    expect(
        Achievement::where('user_id', $user->id)
            ->where('achievement_type', $achievementType)
            ->exists()
    )->toBeTrue();
});

test('getUserProgress returns correct achievement stats', function () {
    // Arrange
    $user = UserDto::fromModel($this->user);  // Create some achievements and progress
    Achievement::factory()->create([
        'user_id' => $user->id,
        'achievement_type' => AchievementType::FIRST_PURCHASE,
    ]);

    AchievementProgress::factory()->create([
        'user_id' => $user->id,
        'achievement_type' => AchievementType::SPEND_AMOUNT_100,
        'current_value' => 50,
        'target_value' => 100,
    ]);

    // Act
    $result = $this->achievementService->getUserProgress($user);

    // Assert
    expect($result)
        ->toHaveKey('progress')
        ->toHaveKey('unlocked')
        ->toHaveKey('total_unlocked')
        ->and($result['total_unlocked'])->toBe(1)
        ->and($result['progress'])->toBeArray()
        ->and($result['unlocked'])->toBeArray();
});

test('getUserAchievementByType returns correct achievement', function () {
    // Arrange
    $user = UserDto::fromModel($this->user);
    $achievement = Achievement::factory()->create([
        'user_id' => $user->id,
        'achievement_type' => AchievementType::FIRST_PURCHASE,
    ]);

    // Act
    $result = $this->achievementService->getUserAchievementByType($user, AchievementType::FIRST_PURCHASE);

    // Assert
    expect($result->id)->toBe($achievement->id)
        ->and($result->achievement_type)->toBe(AchievementType::FIRST_PURCHASE);
});
