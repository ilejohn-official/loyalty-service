<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Badge;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ShowBadgeRequest;
use App\Http\Resources\BadgeResource;
use App\Http\Resources\UserResource;

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
        'badges' => BadgeResource::collection($badges),
        'total_earned' => $badges->count(),
        'highest_level' => $badges->max('level'),
        'user' => new UserResource($user),
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
  public function show(ShowBadgeRequest $request, User $user, string $badgeType): JsonResponse
  {
    $badge = Badge::query()
      ->forUser($user->id)
      ->where('badge_type', $badgeType)
      ->firstOrFail();
    return response()->json([
      'data' => new BadgeResource($badge),
    ]);
  }
}
