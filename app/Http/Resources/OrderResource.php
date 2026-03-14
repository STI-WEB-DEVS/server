<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'uuid'         => $this->uuid,
            'customer'     => [
                'name'  => $this->customer->name,
                'email' => $this->customer->email,
            ],
            'items'        => $this->items->map(function ($item) {
                return [
                    'product'    => $item->product->name,
                    'unit_price' => $item->unit_price,
                    'quantity'   => $item->quantity,
                    'subtotal'   => $item->unit_price * $item->quantity,
                ];
            }),
            'total_amount' => $this->total_amount,
            'created_at'   => $this->created_at->toDateTimeString(),
            'updated_at'   => $this->updated_at->toDateTimeString(),
        ];
    }
}