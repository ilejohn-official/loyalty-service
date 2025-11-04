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
    Route::get('users/{user}/achievements', [AchievementController::class, 'index']);
    Route::get('users/{user}/achievements/{achievementType}', [AchievementController::class, 'show']);

    Route::get('users/{user}/badges', [BadgeController::class, 'index']);
    Route::get('users/{user}/badges/{badgeType}', [BadgeController::class, 'show']);

    Route::prefix('loyalty')->group(function () {
        Route::get('users/{user}/points', [LoyaltyController::class, 'index']);
        Route::get('users/{user}/summary/{yearMonth}', [LoyaltyController::class, 'monthlySummary']);
        Route::post('users/{user}/cashback', [LoyaltyController::class, 'processCashback']);
    });

    // Admin routes (protected by auth:sanctum middleware)
    Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('users/achievements', [UserAchievementController::class, 'index']);
        Route::get('users/{user}/achievements', [UserAchievementController::class, 'show']);
    });
});
