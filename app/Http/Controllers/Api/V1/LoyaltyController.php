<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Models\LoyaltyTransaction;
use App\Models\PaymentTransaction;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\LoyaltyTransactionResource;
use App\Http\Requests\Api\V1\MonthlySummaryRequest;
use App\Http\Requests\Api\V1\ProcessCashbackRequest;

class LoyaltyController extends Controller
{
  /**
   * Get user's loyalty points and transactions.
   *
   * @param  \App\Models\User  $user
   * @return \Illuminate\Http\JsonResponse
   */
  public function index(User $user): JsonResponse
  {
    $transactions = LoyaltyTransaction::query()
      ->forUser($user->id)
      ->with(['user'])
      ->latest('created_at')
      ->get();

    $totalPoints = $transactions->sum('points_earned');

    return response()->json([
      'data' => [
        'transactions' => LoyaltyTransactionResource::collection($transactions),
        'total_points' => $totalPoints,
        'user' => new UserResource($user),
      ],
    ]);
  }

  /**
   * Process a cashback payment.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\User  $user
   * @return \Illuminate\Http\JsonResponse
   */
  public function processCashback(ProcessCashbackRequest $request, User $user): JsonResponse
  {
    $validated = $request->validated();    // Create pending payment transaction
    $payment = PaymentTransaction::create([
      'user_id' => $user->id,
      'amount' => $validated['amount'],
      'status' => 'pending',
    ]);

    // TODO: Integrate with payment provider (Paystack/Flutterwave)
    // This would typically be handled by a job or service class

    return response()->json([
      'data' => [
        'payment' => $payment,
        'message' => 'Cashback payment initiated',
      ],
    ]);
  }

  /**
   * Get monthly loyalty summary.
   *
   * @param  \App\Models\User  $user
   * @param  string  $yearMonth Format: YYYYMM
   * @return \Illuminate\Http\JsonResponse
   */
  public function monthlySummary(MonthlySummaryRequest $request, User $user, string $yearMonth): JsonResponse
  {
    $transactions = LoyaltyTransaction::query()
      ->forUser($user->id)
      ->forMonth($yearMonth)
      ->get();

    return response()->json([
      'data' => [
        'month' => $yearMonth,
        'total_points' => $transactions->sum('points_earned'),
        'total_amount' => $transactions->sum('amount'),
        'transaction_count' => $transactions->count(),
      ],
    ]);
  }
}
