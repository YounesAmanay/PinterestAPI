<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PinCollection;
use App\Http\Resources\PinResource;
use App\Models\Pin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PinController extends Controller
{
    public function index()
    {
        $user = User::find(Auth::id());
        $pins = $user->pins()->orderBy('created_at', 'desc')->get();
        return new PinCollection($pins);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|image|min:1048',
            'title' => 'required|string|min:10',
            'descreption' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'board_id' => 'nullable|exists:boards,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $pin = new Pin();
        $pin->user_id = Auth::id();
        $pin->descreption = $request->descreption;
        $pin->category_id = $request->category_id;
        $pin->title = $request->title;
        $pin->board_id = $request->board_id;

        // todo : store the în the disk and retrive the path
        $file = $request->pin;
        $path = $file->store('pins', 'public');
        $pin->pin = $path;
        $pin->save();

        return response()->json([
            'data' => new PinResource($pin),
            'message' => 'Pin created successfully!',
        ], 201);
    }

    public function show(Pin $pin)
    {
        return new PinResource($pin);
    }

    public function update(Request $request, Pin $pin)
    {
        if (Auth::id() !== $pin->user_id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'pin' => 'nullable|image|min:1048',
            'title' => 'required|string|min:10',
            'descreption' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'board_id' => 'nullable|exists:boards,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $pin->descreption = $request->descreption;
        $pin->category_id = $request->category_id;
        $pin->title = $request->title;
        $pin->board_id = $request->board_id;

        if ($request->hasFile('pin')) {
            Storage::delete('public/' . $pin->pin);
            $file = $request->pin;
            $path = $file->store('pins', 'public');
            $pin->pin = $path;
        }

        $pin->save();

        return response()->json(['message' => 'Pin updated successfully'], 200);
    }


    public function destroy(Pin $pin)
    {
        if (Auth::id() !== $pin->user_id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $pin->delete();
        Storage::delete('public/' . $pin->pin);
        return response()->json(['message' => 'Pin deleted successfully'], 200);
    }
}
