<?php

namespace App\Http\Controllers\api;

use App\Events\UserFollowed;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowContorller extends Controller
{
    public function toggleFollow(User $follow)
    {
        $user = User::find(Auth::id());

        if ($user->is($follow)) {
            return response()->json(['message' => 'You cannot follow/unfollow yourself.'], 422);
        }

        if ($user->following->contains($follow)) {
            $user->following()->detach($follow);
            return response()->json(['message' => 'Unfollowed'], 200);
        } else {
            $user->following()->attach($follow);
            event(new UserFollowed($user, $follow));
            return response()->json(['message' => 'Followed'], 200);
        }
    }

    public function isFollowed(User $follow)
    {
        $user = User::find(Auth::id());

        if ($user->is($follow)) {
            return response()->json(['message' => 'You cannot follow/unfollow yourself.'], 422);
        }

        return response()->json(['isFollowed' => $user->following->contains($follow)], 200);
    }
}
