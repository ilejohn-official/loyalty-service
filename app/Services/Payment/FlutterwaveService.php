<?php

namespace App\Services\Payment;

use App\Models\User;
use App\Contracts\Payment\PaymentServiceInterface as ContractPaymentServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FlutterwaveService implements ContractPaymentServiceInterface
{
  protected string $secretKey;
  protected string $baseUrl;

  public function __construct()
  {
    $config = config('loyalty.providers.flutterwave');
    $this->secretKey = $config['secret_key'];
    $this->baseUrl = $config['base_url'];
  }

  public static function getConfigKey(): string
  {
    return 'flutterwave';
  }

  public function processCashback(User $user, float $amount): array
  {
    try {
      $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $this->secretKey,
        'Content-Type' => 'application/json',
      ])->post($this->baseUrl . '/transfers', [
        'account_bank' => $user->flw_bank_code,
        'account_number' => $user->flw_account_number,
        'amount' => $amount,
        'narration' => 'Loyalty Program Cashback',
        'currency' => 'NGN',
        'reference' => 'LYT-' . uniqid(),
        'callback_url' => config('app.url') . '/api/v1/callbacks/flutterwave'
      ]);

      if ($response->successful()) {
        return [
          'success' => true,
          'reference' => $response->json()['data']['reference'],
          'message' => 'Cashback processed successfully'
        ];
      }

      Log::error('Flutterwave cashback failed', [
        'error' => $response->json(),
        'user' => $user->id,
        'amount' => $amount
      ]);

      return [
        'success' => false,
        'message' => 'Failed to process cashback'
      ];
    } catch (\Exception $e) {
      Log::error('Flutterwave cashback error', [
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
      ])->get($this->baseUrl . '/transactions/' . $reference . '/verify');

      return $response->successful() && $response->json()['data']['status'] === 'successful';
    } catch (\Exception $e) {
      Log::error('Flutterwave verification error', [
        'error' => $e->getMessage(),
        'reference' => $reference
      ]);

      return false;
    }
  }
}
