<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable  = ['name'];

    function pins(){
        return $this->hasMany(Pin::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
