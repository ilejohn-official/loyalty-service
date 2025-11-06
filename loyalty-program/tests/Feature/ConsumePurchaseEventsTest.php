<?php

use App\Contracts\Messaging\MessageQueueInterface;
use App\Jobs\ProcessPurchaseEvent;
use Illuminate\Support\Facades\Bus;

test('consume purchase events processes queued messages', function () {
    Bus::fake();

    // Simulate external service publishing an event
    $queue = app(MessageQueueInterface::class);
    $payload = [
        'user_id' => 1,
        'amount' => 100.0,
        'reference' => 'REF12345',
    ];
    $queue->publish($payload);

    $this->artisan('consume:purchases')
        ->expectsOutput('Starting purchase event consumer...')
        ->expectsOutput('Processing purchase event: '.json_encode($payload))
        ->expectsOutput('All purchase events processed.')
        ->assertExitCode(0);

    Bus::assertDispatchedSync(ProcessPurchaseEvent::class, function ($job) {
        return $job->userId === 1
          && $job->amount === 100.0
          && $job->transactionReference === 'REF12345';
    });

    $this->assertEmpty($queue->messages);
});
