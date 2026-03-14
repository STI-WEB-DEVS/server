<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid'     => $this->uuid,
            'order_id' => $this->order->uuid,
            'product'  => [
                'uuid'  => $this->product->uuid,
                'name'  => $this->product->name,
                'price' => $this->product->price,
            ],
            'quantity' => $this->quantity,
            'subtotal' => $this->quantity * $this->product->price,
        ];
    }
}