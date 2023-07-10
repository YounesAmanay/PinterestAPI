<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $user = User::find(Auth::id());

        // Retrieve all chats for the authenticated user
        $chats = $user->chats()
            ->with(['lastMessage' => function ($query) {
                $query->where(function ($query) {
                    $query->where('receiver_id', auth()->id())
                        ->where('receiver_vue', true);
                })
                    ->orWhere(function ($query) {
                        $query->where('sender_id', auth()->id())
                            ->where('sender_vue', true);
                    })
                    ->orderBy('created_at', 'desc');
            }])
            ->get();

        // Order chats by last activity
        $chats = $chats->sortByDesc(function ($chat) {
            return optional($chat->lastMessage)->created_at;
        });

        return ChatResource::collection($chats);
    }


    public function store(Request $request)
    {
        $receiver_id = $request->input('receiver_id');
        $sender = User::find(Auth::id());

        // check if chat already exists between sender and receiver
        $chat = Chat::whereHas('users', function ($query) use ($sender) {
            $query->where('users.id', $sender->id);
        })->whereHas('users', function ($query) use ($receiver_id) {
            $query->where('users.id', $receiver_id);
        })->first();

        if (!$chat) {
            // chat does not exist, create new chat
            $chat =new Chat();
            $chat->user1_id = $sender->id;
            $chat->user2_id = $receiver_id;
            $chat->save();
            $chat->users()->attach([$sender->id, $receiver_id]);

            $message = new Message();
            $message->content = 'Hello there';
            $message->sender_id = $sender->id;
            $message->receiver_id = $receiver_id;
            $chat->messages()->save($message);
        }

        // get receiver name
        $receiver = User::find($receiver_id);
        $receiver_name = $receiver->name;

        return response()->json([
            'chat_id' => $chat->id,
            'receiver_name' => $receiver_name,
            'sender_id' => $sender->id,
        ]);
    }



    public function show($chatId, Request $request)
    {
        $user = Auth::user();
        $chat = Chat::findOrFail($chatId);

        $otherUser = $chat->users()->where('id', '!=', $user->id)->first();
        if (!$user->chats->contains($chat)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = $chat->messages()
            ->where(function ($query) use ($user) {
                $query->where(function ($query) use ($user) {
                    $query->where('sender_id', $user->id)
                        ->where('sender_vue', true);
                })->orWhere(function ($query) use ($user) {
                    $query->where('receiver_id', $user->id)
                        ->where('receiver_vue', true);
                });
            })
            ->orderBy('created_at')
            ->get();

        if (count($messages) > 0) {
            $chat->messages()
                ->where('receiver_id', $user->id)
                ->update(['is_read' => true]);

            return [
                'user_id' => $otherUser->id,
                'user_name' => $otherUser->name,
                'messages' => MessageResource::collection($messages),
            ];
        }

        return response()->json([]);
    }


    public function destroy($chatId)
    {

        $user = User::find(Auth::id());
        if (!$user->chats->contains($chatId)) {
            // The chat does not belong to the authenticated user
            // Handle this case as appropriate (e.g. return an error response)
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $chat = Chat::findOrFail($chatId);
        Message::where('chat_id', $chatId)
            ->where('sender_id', $user->id)
            ->update(['sender_vue' => false]);
        Message::where('chat_id', $chatId)
            ->where('receiver_id', $user->id)
            ->update(['receiver_vue' => false]);
        $user->chats()->detach($chat);


        return response()->json(['message' => "chat deleted with success"]);
    }
}
