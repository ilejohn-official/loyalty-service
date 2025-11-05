<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\LoyaltyService;
use App\Services\UserClient;
use Illuminate\Http\JsonResponse;
use App\Jobs\ProcessPurchaseEvent;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\MonthlySummaryRequest;
use App\Http\Requests\Api\V1\ProcessPurchaseRequest;

class LoyaltyController extends Controller
{
  public function __construct(
    protected LoyaltyService $loyaltyService
  ) {}

  /**
   * Get user's loyalty points and transactions.
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

    return response()->json([
      'data' => $this->loyaltyService->getUserStats($user)
    ]);
  }

  /**
   * Process a purchase event.
   *
   * @param  ProcessPurchaseRequest  $request
   * @param  int  $user_id
   * @return \Illuminate\Http\JsonResponse
   */
  public function processPurchase(ProcessPurchaseRequest $request, int $user_id): JsonResponse
  {
    ProcessPurchaseEvent::dispatch(
      $user_id,
      $request->validated('amount'),
      $request->validated('transaction_reference')
    );

    return response()->json([
      'message' => 'Purchase event queued for processing',
      'status' => 'pending'
    ]);
  }

  /**
   * Get monthly loyalty summary.
   *
   * @param  MonthlySummaryRequest  $request
   * @param  int  $user_id
   * @param  string  $yearMonth
   * @return \Illuminate\Http\JsonResponse
   */
  public function monthlySummary(MonthlySummaryRequest $request, int $user_id, string $yearMonth, UserClient $userClient): JsonResponse
  {
    $user = $userClient->getById($user_id);

    if (! $user) {
      return response()->json(['message' => 'User not found'], 404);
    }

    return response()->json([
      'data' => $this->loyaltyService->getMonthlySummary($user, $yearMonth)
    ]);
  }
}
