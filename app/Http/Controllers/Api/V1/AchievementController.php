<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\AchievementService;
use App\Services\BadgeService;
use App\Services\UserClient;
use Illuminate\Http\JsonResponse;

class AchievementController extends Controller
{
    public function __construct(
        protected AchievementService $achievementService,
        protected BadgeService $badgeService
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
        $badgeData = $this->badgeService->getUserBadges($user);

        return response()->json([
            'data' => array_merge(
                $achievementData,
                $badgeData,
                ['user' => new UserResource($user)]
            ),
        ]);
    }
}
