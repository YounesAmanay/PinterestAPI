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
    public function show()
    {
        $user = Auth::user();
        return new UserResource($user);
        //todo : return the user follower and followee count along with his boards and pins
    }

    public function update(Request $request , User $user)
    {
        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['sometimes', 'min:8', 'confirmed'],
        ]);

        if(Auth::id() !== $user->id){
            return response()->json('Unauthorized' , 401);
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

    public function setProfile(Request $request , User $user)
    {

        $validator =Validator::make($request->all() , [
            'profile' => ['required', 'image', 'max:2048', 'dimensions:min_width=100,min_height=100,max_width=500,max_height=500']

        ]);

        if ($validator -> fails()) {
            return response() -> json($validator -> errors(), 422);
        }

        // Check if the authenticated user is authorized to update the profile
        if(Auth::id() !== $user->id){
            return response()->json('Unauthorized' , 401);
        }

        // Delete the old profile if it exists
        if ($user->profile && Storage::exists('public/'.$user->profile)) {
            storage::delete('public/'.$user->profile);
        }

        $image = $request->file('profile');
        $name = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('profiles', $name, 'public');
        $user -> profile = $path;
        $user -> save();
        return response()->json('ok');
    }

    public function getProfile()
    {
        $user = Auth::user();
        //check if the user has profile
        if($user->profile && Storage::exists('public/'.$user->profile)) {
            $path = storage_path('app/public/' . $user->profile);
            return response()->file($path);
        }

        return response()->json(['message' => 'Profile not found'], 401);
    }


    public function destroy(User $user)
    {
        // Check if the authenticated user has permission to delete the user
        if (Auth::id() === $user->id) {
            $user->delete();
            return response()->json(['message'=>'Account deleted'], 201);
        }

        return response()->json('Unauthorized', 401);
    }
}
