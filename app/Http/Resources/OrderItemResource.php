<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
        'product_uuid' => $this->product->uuid,
        'product_name' => $this->product->name,
        'quantity' => $this->quantity,
        'unit_price' => number_format($this->unit_price, 2),
        'subtotal' => $this->quantity * $this->unit_price
    ];
    }
}