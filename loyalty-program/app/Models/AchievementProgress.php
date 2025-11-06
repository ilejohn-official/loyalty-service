<?php

namespace App\Models;

use App\Enums\AchievementType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property AchievementType $achievement_type
 * @property float $current_value
 * @property float $target_value
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class AchievementProgress extends Model
{
    use HasFactory;

    protected $table = 'achievement_progress';

    protected $fillable = [
        'user_id',
        'achievement_type',
        'current_value',
        'target_value',
    ];

    protected $casts = [
        'current_value' => 'float',
        'target_value' => 'float',
        'achievement_type' => AchievementType::class,
    ];
}
