<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\AchievementType;
use App\Http\Controllers\Controller;
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
     */
    public function index(int $userId, UserClient $userClient): JsonResponse
    {
        $user = $userClient->getById($userId);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $achievementData = $this->achievementService->getUserProgress($user);

        return response()->json([
            'data' => array_merge(
                $achievementData,
                ['user' => new UserResource($user)]
            ),
        ]);
    }

    /**
     * Get specific achievement details.
     */
    public function show(int $userId, AchievementType $achievementType, UserClient $userClient): JsonResponse
    {
        $user = $userClient->getById($userId);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'data' => new AchievementResource(
                $this->achievementService->getUserAchievementByType($user, $achievementType)
            ),
        ]);
    }
}
