<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'uuid'         => $this->uuid,
            'status'       => 'pending',
            'total_amount' => number_format((float) $this->total_amount, 2, '.', ''),
            'customer'     => $this->when($this->relationLoaded('customer') && $this->customer, [
                'uuid'  => $this->customer?->uuid,
                'name'  => $this->customer?->name,
                'email' => $this->customer?->email,
            ]),
            'items'        => $this->when(
                $this->relationLoaded('items'),
                fn () => $this->items->map(fn ($item) => [
                    'product_uuid' => $item->product?->uuid,
                    'product_name' => $item->product?->name,
                    'unit_price'   => number_format((float) $item->unit_price, 2, '.', ''),
                    'quantity'     => $item->quantity,
                    'subtotal'     => number_format((float) ($item->unit_price * $item->quantity), 2, '.', ''),
                ])
            ),
            'created_at'   => $this->created_at?->toDateTimeString(),
        ];
    }
}