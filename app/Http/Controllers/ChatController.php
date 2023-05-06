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
        $lastUpdate = $request->input('lastUpdate');
        $timeout = 30; // set timeout in seconds
        $start = time();
        while ((time() - $start) < $timeout) {
            $user = User::find(Auth::id());
            if (empty($lastUpdate)) {
                // first time loading chats, return all chats
                $chats = $user->chats()
                    ->with(['lastMessage' => function ($query) {
                        $query->orderBy('created_at', 'desc');
                    }])
                    ->get();
            } else {
                // not first time loading chats, check for new updates
                $chats = $user->chats()
                    ->with(['lastMessage' => function ($query) {
                        $query->orderBy('created_at', 'desc');
                    }])
                    ->whereHas('lastMessage', function ($query) use ($lastUpdate) {
                        $query->where('created_at', '>', $lastUpdate);
                    })
                    ->get();
            }
            if (count($chats) > 0) {
                return ChatResource::collection($chats);
            }
            sleep(1);
        }
        return response()->json([]);
    }

    public function show($chatId, Request $request)
    {
        $lastUpdate = $request->input('lastUpdate');
        $timeout = 30; // set timeout in seconds
        $start = time();
        while ((time() - $start) < $timeout) {
            $user = Auth::user();
            $chat = Chat::findOrFail($chatId);
            if (!$user->chats->contains($chat)) {
                // The chat does not belong to the authenticated user
                // Handle this case as appropriate (e.g. return an error response)
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            if (empty($lastUpdate)) {
                // first time loading messages, return all messages
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
            } else {
                // not first time loading messages, check for new updates
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
                    ->where('created_at', '>', $lastUpdate)
                    ->orderBy('created_at')
                    ->get();
            }
            if (count($messages) > 0) {
                return [
                    'chat' => new ChatResource($chat),
                    'messages' => MessageResource::collection($messages),
                ];
            }
            sleep(1);
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
