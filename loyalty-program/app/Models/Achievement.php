<?php

namespace App\Models;

use App\Enums\AchievementType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Achievement
 *
 * @property int $id
 * @property int $user_id
 * @property string $achievement_type
 * @property \Illuminate\Support\Carbon|null $unlocked_at
 * @property array $metadata
 */
class Achievement extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'achievement_type',
        'unlocked_at',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'unlocked_at' => 'datetime',
        'metadata' => 'array',
        'achievement_type' => AchievementType::class,
    ];

    /**
     * Scope a query to a specific user.
     */
    public function scopeForUser(Builder $query, $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}
