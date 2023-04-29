<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['content' , 'user_id' , 'pin_id'];
    
    public function pin()
    {
        return $this->belongsTo(Pin::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }}
