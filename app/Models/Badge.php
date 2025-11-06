<?php

namespace App\Models;

use App\Enums\BadgeType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Badge
 *
 * @property int $id
 * @property int $user_id
 * @property string $badge_type
 * @property int $level
 * @property \Illuminate\Support\Carbon|null $earned_at
 */
class Badge extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'badge_type',
        'level',
        'earned_at',
    ];

    protected $casts = [
        'level' => 'integer',
        'earned_at' => 'datetime',
        'badge_type' => BadgeType::class,
    ];

    /**
     * Scope to a user's badges.
     */
    public function scopeForUser(Builder $query, $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}
