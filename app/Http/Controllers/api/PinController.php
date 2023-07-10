<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PinCollection;
use App\Http\Resources\PinResource;
use App\Models\Board;
use App\Models\Pin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PinController extends Controller
{
    public function index()
    {
        $user = User::find(Auth::id());
        $pins = $user->pins()->orderBy('created_at', 'desc')->get();
        return new PinCollection($pins);
    }
    
    public function home(Request $request)
    {
        $user = User::find(Auth::id());
        $categoryIds = $user->categories->pluck('id');

        $pins = Pin::whereIn('category_id', $categoryIds)
            ->orderBy('created_at', 'desc')
            ->get();

        return new PinCollection($pins);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|image',
            'title' => 'required|string|min:5',
            'description' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'board_id' => [
                'required',
                Rule::exists('boards', 'id')->where(function ($query) {
                    $query->where('user_id', Auth::id());
                }),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $pin = new Pin();
        $pin->user_id = Auth::id();
        $pin->description = $request->description;
        $pin->category_id = $request->category_id;
        $pin->title = $request->title;
        $pin->board_id = $request->board_id;

        // Store the pin on disk and retrieve the path
        $file = $request->file('pin');
        $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/pins', $fileName);
        $pin->pin = $fileName;
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
            'pin' => 'nullable|image',
            'title' => 'required|string|min:10',
            'description' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'board_id' => 'required|exists:boards,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $pin->description = $request->input('description');
        $pin->category_id = $request->input('category_id');
        $pin->title = $request->input('title');
        $pin->board_id = $request->input('board_id');

        if ($request->hasFile('pin')) {
            $oldPinPath = 'public/' . $pin->pin;
            if (Storage::exists($oldPinPath)) {
                Storage::delete($oldPinPath);
            }

            $file = $request->file('pin');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/pins', $fileName);
            $pin->pin = $fileName;
        }

        $pin->save();

        return response()->json(['message' => 'Pin updated successfully'], 200);
    }

    public function pin(Pin $pin)
    {
        $path = Storage::path('public/pins/' . $pin->pin);
        return response()->file($path);
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

    public function repin(Request $request, Pin $pin)
    {
        $user = User::find(Auth::id());
        $validator = Validator::make($request->all(), [
            'board_id' => 'required|exists:boards,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Check if the user owns the pin
        if (Auth::check() && $pin->user_id == $user->id) {
            return response()->json(['message' => 'You cannot repin your own pin'], 403);
        }

        $user->savedPins()->attach($pin->id);

        return response()->json(['message' => 'Pin repinned successfully'], 200);
    }

}
