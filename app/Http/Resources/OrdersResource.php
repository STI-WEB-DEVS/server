<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrdersResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid'         => $this->uuid,
            'total_amount' => $this->total_amount,
            'created_at'   => $this->created_at,
            'customer'     => $this->whenLoaded('customer', fn() => [
                'uuid'  => $this->customer->uuid,
                'name'  => $this->customer->name,
                'email' => $this->customer->email,
            ]),
            'items' => $this->whenLoaded('items', fn() =>
                $this->items->map(fn($item) => [
                    'product_uuid' => $item->product->uuid ?? null,
                    'product_name' => $item->product->name ?? 'Unknown',
                    'quantity'     => $item->quantity,
                    'unit_price'   => $item->unit_price,
                    'subtotal'     => round($item->quantity * $item->unit_price, 2),
                ])
            ),
        ];
    }
}
