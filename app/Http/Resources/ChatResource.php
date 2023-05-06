<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    static $wrap = false;
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
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
        ];;
    }
}
