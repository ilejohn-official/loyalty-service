<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use App\Contracts\Payment\PaymentServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the payment service based on the configured provider
        $this->app->bind(PaymentServiceInterface::class, function ($app) {
            $providerName = config('loyalty.payment_provider');

            $providerConfig = config("loyalty.providers.{$providerName}");

            $providerClass = $providerConfig['class'] ?? null;

            if (! $providerClass || ! class_exists($providerClass) || ! is_subclass_of($providerClass, PaymentServiceInterface::class)) {
                throw new \InvalidArgumentException("Invalid payment provider configured: {$providerName}");
            }

            return $app->make($providerClass);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
