<?php

namespace App\Services\Payment;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Contracts\Payment\PaymentServiceInterface;

class PaystackService implements PaymentServiceInterface
{
  protected string $secretKey;
  protected string $baseUrl;

  public function __construct()
  {
    $config = config('loyalty.providers.paystack');
    $this->secretKey = $config['secret_key'];
    $this->baseUrl = $config['base_url'];
  }

  public static function getConfigKey(): string
  {
    return 'paystack';
  }

  public function processCashback(User $user, float $amount): array
  {
    try {
      $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $this->secretKey,
        'Content-Type' => 'application/json',
      ])->post($this->baseUrl . '/transfer', [
        'source' => 'balance',
        'amount' => $amount * 100, // Convert to kobo
        'recipient' => $user->paystack_recipient_code,
        'reason' => 'Loyalty Program Cashback'
      ]);

      if ($response->successful()) {
        return [
          'success' => true,
          'reference' => $response->json()['data']['reference'],
          'message' => 'Cashback processed successfully'
        ];
      }

      Log::error('Paystack cashback failed', [
        'error' => $response->json(),
        'user' => $user->id,
        'amount' => $amount
      ]);

      return [
        'success' => false,
        'message' => 'Failed to process cashback'
      ];
    } catch (\Exception $e) {
      Log::error('Paystack cashback error', [
        'error' => $e->getMessage(),
        'user' => $user->id,
        'amount' => $amount
      ]);

      return [
        'success' => false,
        'message' => 'Failed to process cashback'
      ];
    }
  }

  public function verifyTransaction(string $reference): bool
  {
    try {
      $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $this->secretKey,
        'Content-Type' => 'application/json',
      ])->get($this->baseUrl . '/transaction/verify/' . $reference);

      return $response->successful() && $response->json()['data']['status'] === 'success';
    } catch (\Exception $e) {
      Log::error('Paystack verification error', [
        'error' => $e->getMessage(),
        'reference' => $reference
      ]);

      return false;
    }
  }
}
