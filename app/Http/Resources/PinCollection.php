<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PinCollection extends ResourceCollection
{
    static $wrap = false ;
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
