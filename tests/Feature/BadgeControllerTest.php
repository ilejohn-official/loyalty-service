<?php

use App\Enums\BadgeType;
use App\Http\Resources\BadgeResource;
use App\Models\Badge;

use function Pest\Laravel\getJson;

test('index returns user badges list', function () {
    // Arrange
    Badge::factory()->goldSpender()->create(['user_id' => $this->user->id]);
    Badge::factory()->silverSpender()->create(['user_id' => $this->user->id]);
    Badge::factory()->loyalCustomer()->create(['user_id' => $this->user->id]);

    // Act
    $response = getJson("/api/v1/users/{$this->user->id}/badges");

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'badges',
                'total_earned',
                'highest_level',
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
            ],
        ])
        ->assertJsonPath('data.total_earned', 3)
        ->assertJsonPath('data.highest_level', 3); // Gold is level 2
});

test('show returns specific badge details', function () {
    // Arrange
    $badge = Badge::factory()->bronzeSpender()->create([
        'user_id' => $this->user->id,
    ]);

    // Act
    $response = getJson("/api/v1/users/{$this->user->id}/badges/".BadgeType::BRONZE_SPENDER->value);

    // Assert
    $response->assertStatus(200)
        ->assertJson([
            'data' => (new BadgeResource($badge))->resolve(),
        ]);
});

test('show returns 404 for non-existent badge', function () {
    // Act
    $response = getJson("/api/v1/users/{$this->user->id}/badges/".BadgeType::VIP_MEMBER->value);

    // Assert
    $response->assertStatus(404);
});
