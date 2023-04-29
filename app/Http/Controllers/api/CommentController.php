<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Pin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{

    public function index(Pin $pin)
    {
        $comments = $pin->comments()->with('user')->get();

        return response()->json(['comments' => $comments]);
    }

    public function store(Request $request, Pin $pin)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|min:3|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $comment = new Comment();
        $comment->content = $request->content;
        $comment->user_id = Auth::id();

        $pin->comments()->save($comment);

        return response()->json(['comment' => $comment], 201);
    }

    public function destroy(Pin $pin , Comment $comment)
    {
        if (Auth::id() !== $comment->user_id) {
            return response()->json('Unauthorized', 401);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
