<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\User;
use App\Models\Badge;
use App\Models\Achievement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class UserAchievementController extends Controller
{
  /**
   * Get all users' achievements and badges.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function index(Request $request): JsonResponse
  {
    $users = User::query()
      ->withCount(['achievements', 'badges'])
      ->when(
        $request->filled('search'),
        fn($query) => $query->where('name', 'like', "%{$request->search}%")
      )
      ->paginate(15);

    return response()->json([
      'data' => $users,
      'meta' => [
        'total_users' => $users->total(),
        'per_page' => $users->perPage(),
        'current_page' => $users->currentPage(),
        'last_page' => $users->lastPage(),
      ],
    ]);
  }

  /**
   * Get detailed achievement and badge information for a specific user.
   *
   * @param  \App\Models\User  $user
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(User $user): JsonResponse
  {
    $achievements = Achievement::query()
      ->forUser($user->id)
      ->get();

    $badges = Badge::query()
      ->forUser($user->id)
      ->get();

    return response()->json([
      'data' => [
        'user' => $user,
        'achievements' => [
          'total' => $achievements->count(),
          'items' => $achievements,
        ],
        'badges' => [
          'total' => $badges->count(),
          'highest_level' => $badges->max('level'),
          'items' => $badges,
        ],
      ],
    ]);
  }
}
