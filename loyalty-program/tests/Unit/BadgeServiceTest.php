<?php

use App\DTOs\UserDto;
use App\Enums\BadgeType;
use App\Events\BadgeUnlocked;
use App\Models\Badge;
use App\Models\LoyaltyTransaction;
use App\Services\BadgeService;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->badgeService = app()->make(BadgeService::class);
});

test('checkAndUnlockBadges awards bronze spender badge when spending threshold is met', function () {
    // Arrange
    Event::fake([BadgeUnlocked::class]);
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

    Event::assertDispatched(BadgeUnlocked::class);
});

test('getUserBadges returns correct badge stats', function () {
    // Arrange
    $user = UserDto::fromModel($this->user);

    // Create some badges
    Badge::factory()->bronzeSpender()->create([
        'user_id' => $user->id,
    ]);

    Badge::factory()->silverSpender()->create([
        'user_id' => $user->id,
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
