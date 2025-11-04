<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\LoyaltyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPurchaseEvent implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public function __construct(
    protected User $user,
    protected float $amount,
    protected string $transactionReference
  ) {}

  public function handle(LoyaltyService $loyaltyService): void
  {
    $loyaltyService->processPurchase($this->user, $this->amount, $this->transactionReference);
  }
}
