<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Jobs\ProcessPurchaseEvent;
use Illuminate\Support\Facades\Validator;

class MockPushPurchase extends Command
{
  /**
   * The name and signature of the console command.
   *
   * Example: php artisan mock:push-purchase 1 12000 REF123
   */
  protected $signature = 'mock:push-purchase {user_id} {amount} {reference}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Push a mock purchase event and process it synchronously';

  public function handle(): int
  {
    $input = [
      'user_id' => $this->argument('user_id'),
      'amount' => $this->argument('amount'),
      'reference' => $this->argument('reference'),
    ];

    $validator = Validator::make($input, [
      'user_id' => ['required', 'integer', 'exists:users,id'],
      'amount' => ['required', 'numeric', 'min:0.01'],
      'reference' => ['required', 'string'],
    ]);

    if ($validator->fails()) {
      foreach ($validator->errors()->all() as $error) {
        $this->error($error);
      }

      return self::FAILURE;
    }

    $userId = (int) $input['user_id'];
    $amount = (float) $input['amount'];
    $reference = (string) $input['reference'];

    $user = User::find($userId);

    // Dispatch synchronously so the ProcessPurchaseEvent runs in-process
    ProcessPurchaseEvent::dispatchSync($user, $amount, $reference);

    $this->info("Mock purchase for user {$userId} processed (amount={$amount}, reference={$reference}).");
    return self::SUCCESS;
  }
}
