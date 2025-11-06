<?php

use App\DTOs\UserDto;
use App\Enums\BadgeType;
use App\Models\Badge;
use App\Models\LoyaltyTransaction;

beforeEach(function () {
    $this->badgeService = app()->make(\App\Services\BadgeService::class);
});

test('checkAndUnlockBadges awards bronze spender badge when spending threshold is met', function () {
    // Arrange
    // Event::fake([BadgeUnlocked::class]);
    $user = UserDto::fromModel($this->user);

    // Create purchases totaling $150 (above $100 threshold)
    LoyaltyTransaction::factory()->create([
        'user_id' => $user->id,
        'type' => 'purchase',
        'amount' => 150.00,
        'points_earned' => 1500,
    ]);

    // Act
    $this->badgeService->checkAndUnlockBadges($user);

    // Assert
    expect(
        Badge::where('user_id', $user->id)
            ->where('badge_type', BadgeType::BRONZE_SPENDER)
            ->exists()
    )->toBeTrue();

    // Event::assertDispatched(BadgeUnlocked::class);
});

test('getUserBadges returns correct badge stats', function () {
    // Arrange
    $user = UserDto::fromModel($this->user);

    // Create some badges
    Badge::factory()->create([
        'user_id' => $user->id,
        'badge_type' => BadgeType::BRONZE_SPENDER,
        'level' => 1,
    ]);

    Badge::factory()->create([
        'user_id' => $user->id,
        'badge_type' => BadgeType::SILVER_SPENDER,
        'level' => 2,
    ]);

    // Act
    $result = $this->badgeService->getUserBadges($user);

    // Assert
    expect($result)
        ->toHaveKey('badges')
        ->toHaveKey('total_earned')
        ->toHaveKey('highest_level')
        ->and($result['total_earned'])->toBe(2)
        ->and($result['highest_level'])->toBe(2);
});

test('getUserBadgeByType returns correct badge', function () {
    // Arrange
    $user = UserDto::fromModel($this->user);
    $badge = Badge::factory()->create([
        'user_id' => $user->id,
        'badge_type' => BadgeType::BRONZE_SPENDER,
        'level' => 1,
    ]);

    // Act
    $result = $this->badgeService->getUserBadgeByType($user, BadgeType::BRONZE_SPENDER);

    // Assert
    expect($result->id)->toBe($badge->id)
        ->and($result->badge_type)->toBe(BadgeType::BRONZE_SPENDER);
});
