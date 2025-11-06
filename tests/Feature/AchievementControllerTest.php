<?php

use function Pest\Laravel\getJson;

test('index returns user achievements and progress', function () {
  // Arrange
  \App\Models\Achievement::factory(2)->create([
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
      'badges',
      'total_earned',
      'highest_level',
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
            ],
        ]);
});
