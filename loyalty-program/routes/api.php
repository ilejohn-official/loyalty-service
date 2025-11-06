<?php

use Illuminate\Support\Facades\Route;
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
    Route::get('users/{userId}/achievements', [AchievementController::class, 'index']);
    Route::post('loyalty/users/{userId}/cashback', [LoyaltyController::class, 'processCashback']);

    // Admin routes (protected by auth:sanctum middleware)
    Route::middleware('admin')->group(function () {
        Route::get('admin/users/achievements', [UserAchievementController::class, 'index']);
    });
});
