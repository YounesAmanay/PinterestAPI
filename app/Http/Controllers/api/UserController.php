<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['sometimes', 'min:8', 'confirmed'],
        ]);

        if (Auth::id() !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    public function setProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile' => ['required', 'image']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::find(Auth::id());

        // Delete the old profile if it exists
        if ($user->profile && Storage::exists('public/profiles/' . $user->profile)) {
            Storage::delete('public/profiles/' . $user->profile);
        }

        $image = $request->file('profile');
        $path = Storage::putFileAs('public/profiles', $image, uniqid() . '.png');
        $user->profile = basename($path);
        $user->save();

        return response()->json(['message' => 'Profile updated successfully']);
    }


    public function getProfile(User $user)
    {
        if ($user->profile) {
            $path = Storage::path('public/profiles/' . $user->profile);
            if (file_exists($path)) {
                return response()
                ->file($path);
            }
        }

        return response()->json(['message' => 'Profile not found'], 404);
    }

    public function destroy(User $user)
    {
        // Check if the authenticated user has permission to delete the user
        if (Auth::id() === $user->id) {
            $user->delete();
            return response()->json(['message' => 'Account deleted'], 201);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
