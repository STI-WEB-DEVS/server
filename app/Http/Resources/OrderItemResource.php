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
            'id'           => $this->id,
            'product'      => [
                'uuid' => $this->product->uuid ?? null,
                'name' => $this->product->name ?? null,
            ],
            'product_uuid' => $this->product->uuid ?? null,
            'product_name' => $this->product->name ?? null,
            'quantity'     => $this->quantity,
            'unit_price'   => $this->unit_price,
            'subtotal'     => $this->quantity * $this->unit_price,
        ];
    }
}