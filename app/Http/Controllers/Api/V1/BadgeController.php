<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\BadgeService;
use App\Services\UserClient;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\BadgeResource;
use App\Http\Requests\Api\V1\ShowBadgeRequest;

class BadgeController extends Controller
{
  public function __construct(
    protected BadgeService $badgeService
  ) {}

  /**
   * Get user's badges.
   *
   * @param  int  $user_id
   * @return \Illuminate\Http\JsonResponse
   */
  public function index(int $user_id, UserClient $userClient): JsonResponse
  {
    $user = $userClient->getById($user_id);

    if (! $user) {
      return response()->json(['message' => 'User not found'], 404);
    }

    $badgeData = $this->badgeService->getUserBadges($user);

    return response()->json([
      'data' => array_merge(
        $badgeData,
        ['user' => new UserResource($user)]
      )
    ]);
  }

  /**
   * Get specific badge details.
   *
   * @param  ShowBadgeRequest  $request
   * @param  int  $user_id
   * @param  string  $badgeType
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(ShowBadgeRequest $request, int $user_id, string $badgeType, UserClient $userClient): JsonResponse
  {
    $user = $userClient->getById($user_id);

    if (! $user) {
      return response()->json(['message' => 'User not found'], 404);
    }

    return response()->json([
      'data' => new BadgeResource(
        $this->badgeService->getUserBadgeByType($user, $badgeType)
      )
    ]);
  }
}
