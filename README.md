# Loyalty Program API

A Laravel-based loyalty program API with achievement tracking, badges, and cashback rewards.

## Payment Provider Configuration

The loyalty program supports multiple payment providers for processing cashback rewards. Currently supported providers:

- Paystack
- Flutterwave

### Setup Instructions

1. Configure your environment variables in `.env`:

```env
# Choose your payment provider (paystack or flutterwave)
LOYALTY_PAYMENT_PROVIDER=paystack

# Paystack Configuration
PAYSTACK_SECRET_KEY=your_paystack_secret_key
PAYSTACK_PUBLIC_KEY=your_paystack_public_key
PAYSTACK_BASE_URL=https://api.paystack.co

# Flutterwave Configuration
FLUTTERWAVE_SECRET_KEY=your_flutterwave_secret_key
FLUTTERWAVE_PUBLIC_KEY=your_flutterwave_public_key
FLUTTERWAVE_ENCRYPTION_KEY=your_flutterwave_encryption_key
FLUTTERWAVE_BASE_URL=https://api.flutterwave.com/v3

# Points and Cashback Configuration
LOYALTY_POINTS_RATIO=100
LOYALTY_MIN_CASHBACK_AMOUNT=10000
LOYALTY_CASHBACK_PERCENTAGE=1.0
```

3. Update your database to include the required user fields:

For Paystack:

```php
// Add to your user migration or create a new migration
$table->string('paystack_recipient_code')->nullable();
```

For Flutterwave:

```php
// Add to your user migration or create a new migration
$table->string('flw_bank_code')->nullable();
$table->string('flw_account_number')->nullable();
```

### Switching Payment Providers

To switch between payment providers:

1. Update your `.env` file:

```env
LOYALTY_PAYMENT_PROVIDER=flutterwave  # or paystack
```

2. Clear your configuration cache:

```bash
php artisan config:clear
```

The application will automatically use the specified provider for all cashback transactions.

### Points and Cashback Configuration

Configure the loyalty program behavior in `config/loyalty.php`:

```php
'points' => [
    'currency_to_point_ratio' => env('LOYALTY_POINTS_RATIO', 100),
    'minimum_cashback_amount' => env('LOYALTY_MIN_CASHBACK_AMOUNT', 10000),
    'cashback_percentage' => env('LOYALTY_CASHBACK_PERCENTAGE', 1.0),
]
```

- `currency_to_point_ratio`: Amount in currency needed to earn 1 point
- `minimum_cashback_amount`: Minimum purchase amount for cashback eligibility
- `cashback_percentage`: Percentage of purchase amount awarded as cashback

### Queue Configuration

The loyalty program uses queues for processing purchases and notifications. Configure your queue driver in `.env`:

```env
QUEUE_CONNECTION=redis  # or database, sqs, etc.
```

Run the queue worker:

```bash
php artisan queue:work
```

#### Local Kafka queue simulation (developer)

For local development and demos, you can simulate the Kafka event queue using the provided Artisan command. This command dispatches the existing purchase job synchronously (in-process) and exercises the same achievement/badge unlocking logic and notifications without requiring Kafka or a queue driver.

Usage:

```bash
php artisan mock:push-purchase {user_id} {amount} {reference}
```

Example:

```bash
php artisan mock:push-purchase 1 12000 REF-TEST-1
```

Note: The previous HTTP mock endpoint was removed; use the artisan command above to simulate purchase events locally.

### Testing Payment Providers

To test the payment providers:

1. Create test accounts with Paystack and Flutterwave
2. Use test API keys in your development environment
3. Test transactions using the providers' test card numbers

Example test endpoints:

```bash
# Process a purchase
POST /api/v1/users/{user_id}/purchases
{
    "amount": 15000,
    "transaction_reference": "TXN_123456789"
}

# Check cashback status
GET /api/v1/users/{user_id}/cashback
```

### User service configuration

The loyalty service resolves user details via a `UserClient`. By default it will try to load a local `User` record from the application's database. If you run the loyalty service as a standalone microservice, configure a user service base URL so it can fetch user details remotely.

Set these environment variables (or edit `config/user.php`):

```env
USER_SERVICE_URL=https://users.service.internal
USER_SERVICE_TIMEOUT=2
USER_SERVICE_INTERNAL_ENDPOINT=/api/v1/internal/users/
```

The `UserClient` will first attempt a local DB lookup and then fall back to calling the configured user service. Controllers and jobs accept a `user_id` and resolve the user through `UserClient` so the loyalty service can run independently from the user datastore.

### Security Considerations

1. Never commit real API keys to version control
2. Use HTTPS for all API endpoints
3. Validate transaction references with payment providers
4. Implement rate limiting for API endpoints
5. Keep payment provider secret keys secure

### Adding New Payment Providers

To add a new payment provider:

1. Create a new service class implementing `PaymentServiceInterface`
2. Add provider configuration to `config/loyalty.php`
3. Update the service provider binding in `AppServiceProvider`

Example:

```php
class NewPaymentProvider implements PaymentServiceInterface
{
    public function processCashback(User $user, float $amount): array
    {
        // Implementation
    }

    public function verifyTransaction(string $reference): bool
    {
        // Implementation
    }
}
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
