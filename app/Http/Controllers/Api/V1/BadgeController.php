<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
  /**
   * Get user's badges.
   *
   * @param  \App\Models\User  $user
   * @return \Illuminate\Http\JsonResponse
   */
  public function index(User $user): JsonResponse
  {
    $badges = Badge::query()
      ->forUser($user->id)
      ->with(['user'])
      ->get();

    return response()->json([
      'data' => [
        'badges' => $badges,
        'total_earned' => $badges->count(),
        'highest_level' => $badges->max('level'),
        'user' => $user->only(['id', 'name']),
      ],
    ]);
  }

  /**
   * Get specific badge details.
   *
   * @param  \App\Models\User  $user
   * @param  string  $badgeType
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(User $user, string $badgeType): JsonResponse
  {
    $badge = Badge::query()
      ->forUser($user->id)
      ->where('badge_type', $badgeType)
      ->firstOrFail();

    return response()->json([
      'data' => $badge,
    ]);
  }
}
