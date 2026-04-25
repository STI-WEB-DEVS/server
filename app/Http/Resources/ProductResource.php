<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid'       => $this->uuid,
            'name'       => $this->name,
            'price'      => number_format((float) $this->price, 2, '.', ''),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}