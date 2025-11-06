<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class LoyaltyTransaction
 *
 * @property int $id
 * @property int $user_id
 * @property float $amount
 * @property TransactionType $type
 * @property int $points_earned
 * @property \Illuminate\Support\Carbon|null $created_at
 */
class LoyaltyTransaction extends Model
{
  use HasFactory;

  protected $fillable = [
    'user_id',
    'amount',
    'type',
    'points_earned',
    'reference',
  ];

  protected $casts = [
    'amount' => 'decimal:2',
    'points_earned' => 'integer',
    'type' => TransactionType::class,
  ];
}
