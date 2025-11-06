<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\ListUserAchievementsRequest;
use App\Models\Achievement;
use App\Models\Badge;
use App\Models\User;
use App\Services\UserClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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

    /**
     * Get detailed achievement and badge information for a specific user.
     */
    public function show(int $user_id, UserClient $userClient): JsonResponse
    {
        $user = $userClient->getById($user_id);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

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
