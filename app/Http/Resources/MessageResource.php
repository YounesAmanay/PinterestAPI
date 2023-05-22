<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'chat_id' => $this->chat_id,
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'sender_name' => $this->getUserName($this->sender_id),
            'receiver_name' => $this->getUserName($this->receiver_id), 
            'sender_vue' => $this->sender_vue,
            'receiver_vue' => $this->receiver_vue,
            'content' => $this->content,
            'is_read' => $this->is_read,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    private function getUserName($userId)
    {
        $user = User::find($userId);
        return $user ? $user->name : null;
    }
}
