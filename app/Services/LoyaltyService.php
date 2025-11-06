<?php

namespace App\Services;

use App\DTOs\UserDto;
use App\Models\LoyaltyTransaction;
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

  public function processPurchase(UserDto $user, float $amount, string $transactionReference): array
  {
    if (! $this->paymentService->verifyTransaction($transactionReference)) {
      return [
        'success' => false,
        'message' => 'Transaction verification failed',
      ];
    }

    // Check for existing transaction with same reference
    $exists = LoyaltyTransaction::query()
      ->where('reference', $transactionReference)
      ->exists();

    if ($exists) {
      return [
        'success' => true,
        'message' => 'Transaction already processed',
        'idempotent' => true,
      ];
    }

    try {
      DB::beginTransaction();

      // Record loyalty transaction (will fail if duplicate due to unique index)
      LoyaltyTransaction::create([
        'user_id' => $user->id,
        'amount' => $amount,
        'type' => 'purchase',
        'points_earned' => $this->calculatePoints($amount),
        'reference' => $transactionReference
      ]);      // Check and unlock achievements
      $this->achievementService->checkAndUnlockAchievements($user, $amount);

      // Check and unlock badges
      $this->badgeService->checkAndUnlockBadges($user);

      // Process cashback if applicable
      $cashback = $this->processCashbackIfEligible($user, $amount);

      DB::commit();

      return [
        'success' => true,
        'message' => 'Purchase processed successfully',
        'cashback' => $cashback,
      ];
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Purchase processing failed', [
        'error' => $e->getMessage(),
        'user' => $user->id,
        'amount' => $amount,
      ]);

      return [
        'success' => false,
        'message' => 'Failed to process purchase',
      ];
    }
  }

  protected function calculatePoints(float $amount): int
  {
    $ratio = config('loyalty.points.currency_to_point_ratio', 100);

    return (int) ($amount / $ratio);
  }

  protected function processCashbackIfEligible(UserDto $user, float $amount): array
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
      ),
    ];
  }

  public function getUserStats(UserDto $user): array
  {
    // Get points total and recent transactions in one query
    $stats = LoyaltyTransaction::query()
      ->where('user_id', $user->id)
      ->selectRaw('
        COALESCE(SUM(CASE WHEN type = "purchase" THEN points_earned ELSE 0 END), 0) as total_points,
        JSON_ARRAYAGG(
          JSON_OBJECT(
            "id", id,
            "amount", amount,
            "points_earned", points_earned,
            "type", type,
            "created_at", created_at
          )
          ORDER BY created_at DESC
          LIMIT 10
        ) as recent_transactions
      ')
      ->first();

    return [
      'total_points' => $stats->total_points,
      'achievements' => $this->achievementService->getUserProgress($user),
      'badges' => $this->badgeService->getUserBadges($user),
      'transactions' => json_decode($stats->recent_transactions ?? '[]', true),
    ];
  }
}
