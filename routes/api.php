<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\BadgeController;
use App\Http\Controllers\Api\V1\LoyaltyController;
use App\Http\Controllers\Api\V1\AchievementController;
use App\Http\Controllers\Api\V1\Admin\UserAchievementController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Public routes
    Route::get('users/{user_id}/achievements', [AchievementController::class, 'index']);
    Route::get('users/{user_id}/achievements/{achievementType}', [AchievementController::class, 'show']);

    Route::get('users/{user_id}/badges', [BadgeController::class, 'index']);
    Route::get('users/{user_id}/badges/{badgeType}', [BadgeController::class, 'show']);

    Route::prefix('loyalty')->group(function () {
        Route::get('users/{user_id}/points', [LoyaltyController::class, 'index']);
        Route::get('users/{user_id}/summary/{yearMonth}', [LoyaltyController::class, 'monthlySummary']);
        Route::post('users/{user_id}/cashback', [LoyaltyController::class, 'processCashback']);
    });

    // Admin routes (protected by auth:sanctum middleware)
    Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('users/achievements', [UserAchievementController::class, 'index']);
        Route::get('users/{user_id}/achievements', [UserAchievementController::class, 'show']);
    });
});
