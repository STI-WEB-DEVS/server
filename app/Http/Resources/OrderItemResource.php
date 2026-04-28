<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'quantity'   => $this->quantity,
            'unit_price' => $this->unit_price,
            'product'    => new ProductResource($this->whenLoaded('product')),
        ];
    }
}