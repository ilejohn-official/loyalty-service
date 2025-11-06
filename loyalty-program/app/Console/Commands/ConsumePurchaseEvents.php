<?php

namespace App\Console\Commands;

use App\Contracts\Messaging\MessageQueueInterface;
use App\Jobs\ProcessPurchaseEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ConsumePurchaseEvents extends Command
{
    protected $signature = 'consume:purchases';

    protected $description = 'Consume purchase events from the configured queue';

    public function handle(MessageQueueInterface $queue): int
    {
        $this->info('Starting purchase event consumer...');

        $queue->consume(function (array $payload) {
            $this->info('Processing purchase event: '.json_encode($payload));

            ProcessPurchaseEvent::dispatchSync(
                $payload['user_id'],
                $payload['amount'],
                $payload['reference']
            );

            Log::info('Purchase event processed', $payload);
        });

        $this->info('All purchase events processed.');

        return self::SUCCESS;
    }
}
