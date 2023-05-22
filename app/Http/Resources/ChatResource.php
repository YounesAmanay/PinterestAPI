<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ChatResource extends JsonResource
{
    static $wrap = false;

    public function toArray($request)
    {
        $user1 = $this->user1;
        $user2 = $this->user2;

        $user = $user1->id === Auth::id() ? $user2 : $user1;
        $id = $user->id;
        $name = $user->name;

        return [
            'id' => $this->id,
            'user_id' => $id,
            'user_name' => $name,
            'last_message' => $this->when(
                $this->lastMessage,
                $this->lastMessage->content
            ),
            'date' => $this->when(
                $this->lastMessage,
                $this->lastMessage->created_at
            ),
            'unread_count' => $this->messages()
                ->where('is_read', false)
                ->where('receiver_id', auth()->id())
                ->count(),
        ];
    }
}
