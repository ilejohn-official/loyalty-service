<?php

namespace App\Jobs;

use App\Services\LoyaltyService;
use App\Services\UserClient;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPurchaseEvent
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $userId,
        public float $amount,
        public string $transactionReference
    ) {}

    public function handle(UserClient $userClient, LoyaltyService $loyaltyService): void
    {
        $user = $userClient->getById($this->userId);

        if (! $user) {
            // If the user can't be resolved, log and stop processing this event.
            // In a production system you might release the job for retry or send to a DLQ.
            \Illuminate\Support\Facades\Log::warning('ProcessPurchaseEvent: user not found', ['user_id' => $this->userId]);

            return;
        }

        $loyaltyService->processPurchase($user, $this->amount, $this->transactionReference);
    }
}
