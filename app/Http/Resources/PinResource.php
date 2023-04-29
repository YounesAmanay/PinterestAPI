<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PinResource extends JsonResource
{
    static $wrap = false;
    public function toArray(Request $request): array
    {
        return [
            "id" =>$this->id,
            "user_id" => $this->user_id,
            "title" => $this->title,
            "pin" => url('api/pin/'.$this->id),
            "category_id" => $this->category_id,
            "descreption" => $this->descreption,
            "board_id" => $this->board_id,
        ];

    }
}
