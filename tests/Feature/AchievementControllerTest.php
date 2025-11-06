<?php

use App\Enums\AchievementType;
use App\Http\Resources\AchievementResource;

use function Pest\Laravel\getJson;

test('index returns user achievements and progress', function () {
    // Arrange
    $achievements = \App\Models\Achievement::factory(2)->create([
        'user_id' => $this->user->id,
    ]);

    \App\Models\AchievementProgress::factory(3)->create([
        'user_id' => $this->user->id,
    ]);

    // Act
    $response = getJson("/api/v1/users/{$this->user->id}/achievements");

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'progress',
                'unlocked',
                'total_unlocked',
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
            ],
        ]);
});

test('show returns specific achievement details', function () {
    // Arrange
    $achievement = \App\Models\Achievement::factory()->create([
        'user_id' => $this->user->id,
        'achievement_type' => AchievementType::FIRST_PURCHASE,
    ]);

    // Act
    $response = getJson("/api/v1/users/{$this->user->id}/achievements/".AchievementType::FIRST_PURCHASE->value);

    // Assert
    $response->assertStatus(200)
        ->assertJson([
            'data' => (new AchievementResource($achievement))->resolve(),
        ]);
});

test('show returns 404 for non-existent achievement', function () {
    // Act
    $response = getJson("/api/v1/users/{$this->user->id}/achievements/".AchievementType::SPEND_AMOUNT_1000->value);

    // Assert
    $response->assertStatus(404);
});
