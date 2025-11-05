<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Contracts\Payment\PaymentServiceInterface;

class LoyaltyService
{
  public function __construct(
    protected AchievementService $achievementService,
    protected BadgeService $badgeService,
    protected PaymentServiceInterface $paymentService
  ) {}

  public function processPurchase(User $user, float $amount, string $transactionReference): array
  {
    if (!$this->paymentService->verifyTransaction($transactionReference)) {
      return [
        'success' => false,
        'message' => 'Transaction verification failed'
      ];
    }

    try {
      DB::beginTransaction();

      // Record loyalty transaction
      $user->loyaltyTransactions()->create([
        'amount' => $amount,
        'type' => 'purchase',
        'points_earned' => $this->calculatePoints($amount),
        'reference' => $transactionReference
      ]);

      // Check and unlock achievements
      $this->achievementService->checkAndUnlockAchievements($user, $amount);

      // Check and unlock badges
      $this->badgeService->checkAndUnlockBadges($user);

      // Process cashback if applicable
      $cashback = $this->processCashbackIfEligible($user, $amount);

      DB::commit();

      return [
        'success' => true,
        'message' => 'Purchase processed successfully',
        'cashback' => $cashback
      ];
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Purchase processing failed', [
        'error' => $e->getMessage(),
        'user' => $user->id,
        'amount' => $amount
      ]);

      return [
        'success' => false,
        'message' => 'Failed to process purchase'
      ];
    }
  }

  protected function calculatePoints(float $amount): int
  {
    $ratio = config('loyalty.points.currency_to_point_ratio', 100);
    return (int) ($amount / $ratio);
  }

  protected function processCashbackIfEligible(User $user, float $amount): array
  {
    $minAmount = config('loyalty.points.minimum_cashback_amount', 10000);
    $percentage = config('loyalty.points.cashback_percentage', 1.0);

    if ($amount >= $minAmount) {
      $cashbackAmount = $amount * ($percentage / 100);
      return $this->paymentService->processCashback($user, $cashbackAmount);
    }

    return [
      'success' => true,
      'message' => sprintf(
        'No cashback applicable. Minimum purchase amount is %s',
        number_format($minAmount, 2)
      )
    ];
  }

  public function getUserStats(User $user): array
  {
    $totalPoints = $user->loyaltyTransactions()
      ->where('type', 'purchase')
      ->sum('points_earned');

    return [
      'total_points' => $totalPoints,
      'achievements' => $this->achievementService->getUserProgress($user),
      'badges' => $this->badgeService->getUserBadges($user),
      'transactions' => $user->loyaltyTransactions()
        ->latest()
        ->take(10)
        ->get()
        ->map(fn($transaction) => [
          'id' => $transaction->id,
          'amount' => $transaction->amount,
          'points_earned' => $transaction->points_earned,
          'type' => $transaction->type,
          'created_at' => $transaction->created_at
        ])
        ->toArray()
    ];
  }

  public function getMonthlySummary(User $user, string $yearMonth): array
  {
    $transactions = DB::table('loyalty_transactions')
      ->where('user_id', $user->id)
      ->whereRaw("DATE_FORMAT(created_at, '%Y%m') = ?", [$yearMonth])
      ->select([
        DB::raw('SUM(points_earned) as total_points'),
        DB::raw('SUM(amount) as total_amount'),
        DB::raw('COUNT(*) as transaction_count')
      ])
      ->first();

    return [
      'month' => $yearMonth,
      'total_points' => $transactions->total_points ?? 0,
      'total_amount' => $transactions->total_amount ?? 0,
      'transaction_count' => $transactions->transaction_count ?? 0,
    ];
  }
}
