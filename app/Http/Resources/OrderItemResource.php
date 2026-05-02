<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'order_uuid'   => $this->order->uuid ?? null,
            'product_uuid' => $this->product->uuid ?? null,
            'name'         => $this->product->name ?? null,
            'quantity'     => $this->quantity,
            'price'        => $this->unit_price,
            'subtotal'     => $this->quantity * $this->unit_price,
        ];
    }
}
