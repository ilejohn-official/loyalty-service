<?php

use App\Services\Payment\FlutterwaveService;
use App\Services\Payment\PaystackService;

return [
    /*
    |--------------------------------------------------------------------------
    | Default Payment Provider
    |--------------------------------------------------------------------------
    |
    | This option controls the default payment provider that will be used for
    | loyalty program cashback payments. You can switch between 'paystack'
    | and 'flutterwave' based on your requirements.
    |
    */
    'payment_provider' => env('LOYALTY_PAYMENT_PROVIDER', 'paystack'),

    /*
    |--------------------------------------------------------------------------
    | Points Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how points are earned and their conversion rates.
    |
    */
    'points' => [
        'currency_to_point_ratio' => 100, // Amount in currency needed to earn 1 point
        'minimum_cashback_amount' => 10000, // Minimum amount for cashback eligibility
        'cashback_percentage' => 1.0, // Cashback percentage (1.0 = 1%)
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Provider Configurations
    |--------------------------------------------------------------------------
    |
    | Configuration for different payment providers. Add your API keys and other
    | settings here. Never commit actual API keys to version control.
    |
    */
    'providers' => [
        'paystack' => [
            'secret_key' => env('PAYSTACK_SECRET_KEY'),
            'public_key' => env('PAYSTACK_PUBLIC_KEY'),
            'base_url' => env('PAYSTACK_BASE_URL', 'https://api.paystack.co'),
            'class' => PaystackService::class,
        ],
        'flutterwave' => [
            'secret_key' => env('FLUTTERWAVE_SECRET_KEY'),
            'public_key' => env('FLUTTERWAVE_PUBLIC_KEY'),
            'base_url' => env('FLUTTERWAVE_BASE_URL', 'https://api.flutterwave.com/v3'),
            'encryption_key' => env('FLUTTERWAVE_ENCRYPTION_KEY'),
            'class' => FlutterwaveService::class,
        ],
    ],
];
