<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowContorller extends Controller
{
    public function toggleFollow(User $user, User $follow)
    {
        if (Auth::id() !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($user->is($follow)) {
            return response()->json(['message' => 'You cannot follow/unfollow yourself.'], 422);
        }

        if($user -> following->contains($follow)){
            $user->following()->detach($follow);
            return response()->json(['message' => 'Unfollowed'], 200);
        }else{
            $user->following()->attach($follow);
            return response()->json(['message' => 'Followed'], 200);
        }
    }
}
