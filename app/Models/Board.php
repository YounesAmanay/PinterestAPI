<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    use HasFactory;

    protected $fillable = ['name' , 'user_id' , 'secret'];

    public function pins()
    {
        return $this->hasMany(Pin::class);
    }

    public function User()
    {
        return $this->belongsTo(User::class);
    }
}
