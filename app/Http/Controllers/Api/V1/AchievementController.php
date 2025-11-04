<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
  /**
   * Get user's achievements and progress.
   *
   * @param  \App\Models\User  $user
   * @return \Illuminate\Http\JsonResponse
   */
  public function index(User $user): JsonResponse
  {
    $achievements = Achievement::query()
      ->forUser($user->id)
      ->with(['user'])
      ->get();

    return response()->json([
      'data' => [
        'achievements' => $achievements,
        'total_unlocked' => $achievements->count(),
        'user' => $user->only(['id', 'name']),
      ],
    ]);
  }

  /**
   * Get specific achievement details.
   *
   * @param  \App\Models\User  $user
   * @param  string  $achievementType
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(User $user, string $achievementType): JsonResponse
  {
    $achievement = Achievement::query()
      ->forUser($user->id)
      ->where('achievement_type', $achievementType)
      ->firstOrFail();

    return response()->json([
      'data' => $achievement,
    ]);
  }
}
