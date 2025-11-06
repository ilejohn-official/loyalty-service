<?php

namespace App\Contracts\Payment;

use App\DTOs\UserDto;

interface PaymentServiceInterface
{
    /**
     * Process a cashback payment to a user.
     *
     * @param  User  $user
     * @return array{success: bool, reference?: string, message: string}
     */
    public function processCashback(UserDto $user, float $amount): array;

    /**
     * Verify a transaction reference with the payment provider.
     */
    public function verifyTransaction(string $reference): bool;
}
