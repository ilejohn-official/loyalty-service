<?php

namespace App\Contracts\Messaging;

interface MessageQueueInterface
{
    /**
     * Mock method for testing or local environments to publish events.
     * In production, only external services publish.
     */
    public function publish(array $payload): void;

    /**
     * Consume messages from the queue.
     *
     * @param  callable  $callback  function (array $payload): void
     */
    public function consume(callable $callback): void;
}
