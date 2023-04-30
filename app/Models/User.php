<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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
        return $this -> belongsToMany(User::class , 'followers' , 'follower_id' , 'followee_id');
    }

    public function pins()
    {
        return $this->hasMany(Pin::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class , 'comments' , 'user_id');
    }

    public function boards()
    {
        return $this->hasMany(Board::class);
    }
}
