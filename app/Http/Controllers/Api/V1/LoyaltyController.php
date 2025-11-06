<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\MonthlySummaryRequest;
use App\Http\Requests\Api\V1\ProcessPurchaseRequest;
use App\Jobs\ProcessPurchaseEvent;
use App\Services\LoyaltyService;
use App\Services\UserClient;
use Illuminate\Http\JsonResponse;

class LoyaltyController extends Controller
{
    public function __construct(
        protected LoyaltyService $loyaltyService
    ) {}

    /**
     * Get user's loyalty points and transactions.
     */
    public function index(int $userId, UserClient $userClient): JsonResponse
    {
        $user = $userClient->getById($userId);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'data' => $this->loyaltyService->getUserStats($user),
        ]);
    }

    /**
     * Process a purchase event.
     */
    public function processPurchase(ProcessPurchaseRequest $request, int $userId): JsonResponse
    {
        ProcessPurchaseEvent::dispatch(
            $userId,
            $request->validated('amount'),
            $request->validated('transaction_reference')
        );

        return response()->json([
            'message' => 'Purchase event queued for processing',
            'status' => 'pending',
        ]);
    }

    /**
     * Get monthly loyalty summary.
     */
    public function monthlySummary(MonthlySummaryRequest $request, int $userId, string $yearMonth, UserClient $userClient): JsonResponse
    {
        $user = $userClient->getById($userId);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'data' => $this->loyaltyService->getMonthlySummary($user, $yearMonth),
        ]);
    }
}
