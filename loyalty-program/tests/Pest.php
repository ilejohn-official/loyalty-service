<?php

use App\Models\User;
use Tests\TestCase;

uses(TestCase::class)->in('Feature', 'Unit');
uses()->beforeEach(function () {
    // Run migrations
    $this->artisan('migrate:fresh');

    // Create regular test user
    $this->user = User::factory()->create();

    // Create admin user
    $this->admin = User::factory()->create([
        'is_admin' => true,
    ]);

    // Bind a simple fake payment service for tests to avoid external HTTP calls
    $this->app->bind(\App\Contracts\Payment\PaymentServiceInterface::class, function () {
        return new class implements \App\Contracts\Payment\PaymentServiceInterface
        {
            public function processCashback(\App\DTOs\UserDto $user, float $amount): array
            {
                return [
                    'success' => true,
                    'reference' => 'TEST-REF-'.uniqid(),
                    'message' => 'Simulated cashback processed',
                ];
            }

            public function verifyTransaction(string $reference): bool
            {
                // In tests we consider all references valid
                return true;
            }
        };
    });
})->in('Feature', 'Unit');
