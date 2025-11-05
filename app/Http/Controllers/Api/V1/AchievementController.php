<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ShowAchievementRequest;
use App\Http\Resources\AchievementResource;
use App\Http\Resources\UserResource;
use App\Services\AchievementService;
use App\Services\UserClient;
use Illuminate\Http\JsonResponse;

class AchievementController extends Controller
{
  public function __construct(
    protected AchievementService $achievementService
  ) {}

  /**
   * Get user's achievements and progress.
   *
   * @param  int  $user_id
   * @return JsonResponse
   */
  public function index(int $user_id, UserClient $userClient): JsonResponse
  {
    $user = $userClient->getById($user_id);

    if (! $user) {
      return response()->json(['message' => 'User not found'], 404);
    }

    $achievementData = $this->achievementService->getUserProgress($user);

    return response()->json([
      'data' => array_merge(
        $achievementData,
        ['user' => new UserResource($user)]
      )
    ]);
  }

  /**
   * Get specific achievement details.
   *
   * @param  ShowAchievementRequest  $request
   * @param  int  $user_id
   * @param  string  $achievementType
   * @return JsonResponse
   */
  public function show(ShowAchievementRequest $request, int $user_id, string $achievementType, UserClient $userClient): JsonResponse
  {
    $user = $userClient->getById($user_id);

    if (! $user) {
      return response()->json(['message' => 'User not found'], 404);
    }

    return response()->json([
      'data' => new AchievementResource(
        $this->achievementService->getUserAchievementByType($user, $achievementType)
      )
    ]);
  }
}
