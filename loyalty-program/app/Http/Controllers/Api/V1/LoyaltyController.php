<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ProcessPurchaseRequest;
use App\Jobs\ProcessPurchaseEvent;
use App\Services\LoyaltyService;
use Illuminate\Http\JsonResponse;

class LoyaltyController extends Controller
{
    public function __construct(
        protected LoyaltyService $loyaltyService
    ) {}

    /**
     * Process a cashback event.
     */
    public function processCashback(ProcessPurchaseRequest $request, int $userId): JsonResponse
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
}
