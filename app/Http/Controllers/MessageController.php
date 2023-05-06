<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|exists:users,id',
            'body' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $sender = Auth::user();
        $receiver = User::find($request->receiver_id);

        // check if chat already exists between sender and receiver
        $chat = Chat::whereHas('users', function ($query) use ($sender) {
            $query->where('users.id', $sender->id);
        })->whereHas('users', function ($query) use ($receiver) {
            $query->where('users.id', $receiver->id);
        })->first();

        if (!$chat) {
            // chat does not exist, create new chat
            $chat = Chat::create();
            $chat->users()->attach([$sender->id, $receiver->id]);
        } else {
            // chat exists, check if receiver is detached
            if (!$chat->users->contains($receiver)) {
                // receiver is detached, re-attach
                $chat->users()->attach($receiver->id);
            }
        }

        // create new message
        $message = new Message();
        $message->body = $request->input('body');
        $message->sender_id = $sender->id;
        $message->receiver_id = $receiver->id;
        $chat->messages()->save($message);

        return new MessageResource($message);
    }

    public function destroy($messageId)
    {
        $user = Auth::user();
        $message = Message::findOrFail($messageId);
        $chat = $message->chat;

        // check if user belongs to chat
        if (!$chat->users->contains($user)) {
            // user does not belong to chat
            // handle this case as appropriate (e.g. return an error response)
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // check if user is sender or receiver of message
        if ($message->sender_id == $user->id) {
            // user is sender, update sender_vue and message content
            $message->sender_vue = false;
            $message->content = 'Message deleted';
            $message->save();
        } elseif ($message->receiver_id == $user->id) {
            // user is receiver, update receiver_vue
            $message->receiver_vue = false;
            $message->save();
        } else {
            // user is neither sender nor receiver
            // handle this case as appropriate (e.g. return an error response)
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([], 204);
    }
}
