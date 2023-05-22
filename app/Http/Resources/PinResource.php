<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PinResource extends JsonResource
{
    static $wrap = false;
    public function toArray(Request $request): array
{
    $path = Storage::path('public/pins/'.$this->pin);
    $imageSize = getimagesize($path);
    $height = $imageSize[1];

    return [
        "id" => $this->id,
        "user_id" => $this->user_id,
        "title" => $this->title,
        "category_id" => $this->category_id,
        "description" => $this->description,
        "board_id" => $this->board_id,
        "created_at" => $this->created_at,
        "user_name" => $this->user->name,
        "image_height" => $height
    ];
}

}
