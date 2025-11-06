<?php

use App\Enums\BadgeType;
use App\Enums\AchievementType;
use App\Models\PaymentTransaction;

use function Pest\Laravel\getJson;

test('complete loyalty program flow', function () {
  // 1. Initially user has no achievements or badges
  $initialData = getJson("/api/v1/users/{$this->user->id}/achievements");

  expect($initialData->json('data.total_unlocked'))->toBe(0)
    ->and($initialData->json('data.total_earned'))->toBe(0);

    // 2. Create multiple payment transactions and process them to trigger achievements
    $payments = PaymentTransaction::factory()->count(5)->create([
        'user_id' => $this->user->id,
        'amount' => 20.00,
        'status' => 'completed',
    ]);

    // Process each payment via the LoyaltyService so achievements/badges are evaluated
    $loyaltyService = app(\App\Services\LoyaltyService::class);
    $userDto = \App\DTOs\UserDto::fromModel($this->user);

  \App\Models\AchievementProgress::factory()->create([
        'user_id' => $this->user->id,
        'achievement_type' => \App\Enums\AchievementType::FIRST_PURCHASE,
        'current_value' => 0,
        'target_value' => \App\Enums\AchievementType::FIRST_PURCHASE->getTargetValue(),
    ]);

    \App\Models\AchievementProgress::factory()->create([
        'user_id' => $this->user->id,
        'achievement_type' => \App\Enums\AchievementType::PURCHASE_COUNT_5,
        'current_value' => 0,
        'target_value' => \App\Enums\AchievementType::PURCHASE_COUNT_5->getTargetValue(),
    ]);

    \App\Models\AchievementProgress::factory()->create([
        'user_id' => $this->user->id,
        'achievement_type' => \App\Enums\AchievementType::SPEND_AMOUNT_100,
        'current_value' => 0,
        'target_value' => \App\Enums\AchievementType::SPEND_AMOUNT_100->getTargetValue(),
    ]);

    foreach ($payments as $index => $p) {
        $loyaltyService->processPurchase($userDto, (float) $p->amount, $p->provider_reference);
        \App\Models\AchievementProgress::where('user_id', $this->user->id)
            ->where('achievement_type', \App\Enums\AchievementType::FIRST_PURCHASE)
            ->first();
    }

    // 3. Verify first purchase achievement unlocked
    $achievements = getJson("/api/v1/users/{$this->user->id}/achievements");
    $unlocked = $achievements->json('data.unlocked');
    $types = array_column($unlocked ?: [], 'achievement_type');

    expect($types)->toContain(AchievementType::FIRST_PURCHASE->value);

    // 4. Make a large purchase to trigger spend achievement
    $largePayment = PaymentTransaction::factory()->create([
        'user_id' => $this->user->id,
        'amount' => 100.00,
        'status' => 'completed',
    ]);

    $loyaltyService->processPurchase($userDto, (float) $largePayment->amount, $largePayment->provider_reference);

  // 5. Verify spend achievement unlocked
  $achievementsAndBadges = getJson("/api/v1/users/{$this->user->id}/achievements");
  $unlocked = $achievementsAndBadges->json('data.unlocked');
    $types = array_column($unlocked ?: [], 'achievement_type');

    expect($types)->toContain(AchievementType::SPEND_AMOUNT_100->value);

  // 6. Verify bronze badge awarded
  $badgeTypes = array_column($achievementsAndBadges->json('data.badges') ?: [], 'badge_type');
    expect($badgeTypes)->toContain(BadgeType::BRONZE_SPENDER->value);
});
