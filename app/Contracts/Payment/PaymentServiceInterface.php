<?php

namespace App\Contracts\Payment;

use App\Models\User;

interface PaymentServiceInterface
{
  /**
   * Process a cashback payment to a user.
   *
   * @param User $user
   * @param float $amount
   * @return array{success: bool, reference?: string, message: string}
   */
  public function processCashback(User $user, float $amount): array;

  /**
   * Verify a transaction reference with the payment provider.
   *
   * @param string $reference
   * @return bool
   */
  public function verifyTransaction(string $reference): bool;

  /**
   * Return the provider config key (e.g. 'paystack' or 'flutterwave')
   *
   * @return string
   */
  public static function getConfigKey(): string;
}
