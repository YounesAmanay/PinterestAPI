<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    static $wrap = false;
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'followers' => $this->followers()->count(),
            'following' => $this->following()->count(),
        ];

        if ($this->profile && Storage::exists('public/'.$this->profile)) {
            $data['profile'] = url('api/profile');
        }

        return $data;
    }
}
