<?php

namespace App\Services\Messaging;

use App\Contracts\Messaging\MessageQueueInterface;
use Illuminate\Support\Facades\Log;

class InMemoryQueueService implements MessageQueueInterface
{
    public array $messages = [];

    public function publish(array $payload): void
    {
        $this->messages[] = $payload;
    }

    public function consume(callable $callback): void
    {
        foreach ($this->messages as $payload) {
            try {
                $callback($payload);
            } catch (\Throwable $e) {
                Log::error('Failed to process message', [
                    'payload' => $payload,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        $this->messages = []; // clear after consuming
    }
}
