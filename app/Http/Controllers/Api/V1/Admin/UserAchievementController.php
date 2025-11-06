<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\ListUserAchievementsRequest;

class UserAchievementController extends Controller
{
    /**
     * Get all users' achievements and badges.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function index(ListUserAchievementsRequest $request): JsonResponse
    {
        // Build subqueries for achievements and badges counts to avoid relying on Eloquent relations
        $achievementsSub = DB::table('achievements')
            ->select('user_id', DB::raw('count(*) as achievements_count'))
            ->groupBy('user_id');

        $badgesSub = DB::table('badges')
            ->select('user_id', DB::raw('count(*) as badges_count'))
            ->groupBy('user_id');

        $query = User::query()
            ->leftJoinSub($achievementsSub, 'a', 'a.user_id', '=', 'users.id')
            ->leftJoinSub($badgesSub, 'b', 'b.user_id', '=', 'users.id')
            ->select('users.*', DB::raw('COALESCE(a.achievements_count, 0) as achievements_count'), DB::raw('COALESCE(b.badges_count, 0) as badges_count'))
            ->when(
                $request->filled('search'),
                fn ($query) => $query->where('users.name', 'like', "%{$request->search}%")
            );

        $users = $query->paginate(15);

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
}
