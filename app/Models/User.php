<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Searchable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'followee_id', 'follower_id');
    }

    public function Following()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'followee_id');
    }

    public function pins()
    {
        return $this->hasMany(Pin::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'comments', 'user_id');
    }

    public function boards()
    {
        return $this->hasMany(Board::class);
    }

    public function searches()
    {
        return $this->hasMany(Search::class);
    }

    public function chats()
    {
        return $this->belongsToMany(Chat::class);
    }

    public function receivedMessages(Chat $chat)
    {
        return $this->hasMany(Message::class)
            ->where('chat_id', $chat->id)
            ->where('receiver_id', $this->id);
    }

    public function sentMessages(Chat $chat)
    {
        return $this->hasMany(Message::class)
            ->where('chat_id', $chat->id)
            ->where('sender_id', $this->id);
    }

    public function hasCompletedOnboarding()
    {
        return $this->onboarding;
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function savedPins()
    {
        return $this->belongsToMany(Pin::class, 'saved_pins', 'user_id', 'pin_id')->withTimestamps();
    }

}
