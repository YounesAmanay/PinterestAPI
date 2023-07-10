<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserFollowed implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $follower;
    public $followed;

    public function __construct(User $follower, User $followed)
    {
        $this->follower = $follower;
        $this->followed = $followed;
    }

    public function broadcastOn()
    {
        return new Channel('user.'.$this->followed->id);
    }

    public function broadcastWith()
    {
        return [
            'followerName' => $this->follower->name,
        ];
    }
}
