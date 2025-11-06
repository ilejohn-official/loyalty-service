<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\BadgeType;
use App\Http\Controllers\Controller;
use App\Http\Resources\BadgeResource;
use App\Http\Resources\UserResource;
use App\Services\BadgeService;
use App\Services\UserClient;
use Illuminate\Http\JsonResponse;

class BadgeController extends Controller
{
    public function __construct(
        protected BadgeService $badgeService
    ) {}

    /**
     * Get user's badges.
     */
    public function index(int $userId, UserClient $userClient): JsonResponse
    {
        $user = $userClient->getById($userId);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $badgeData = $this->badgeService->getUserBadges($user);

        return response()->json([
            'data' => array_merge(
                $badgeData,
                ['user' => new UserResource($user)]
            ),
        ]);
    }

    /**
     * Get specific badge details.
     */
    public function show(int $userId, BadgeType $badgeType, UserClient $userClient): JsonResponse
    {
        $user = $userClient->getById($userId);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'data' => new BadgeResource(
                $this->badgeService->getUserBadgeByType($user, $badgeType)
            ),
        ]);
    }
}
