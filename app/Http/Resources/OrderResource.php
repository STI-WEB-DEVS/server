<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'uuid'         => $this->uuid,
            'customer'     => [
                'id'   => $this->customer->id,
                'name' => $this->customer->name,
            ],
            'total_amount' => $this->total_amount,
            'items'        => $this->items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'product'    => $item->product->name,
                    'quantity'   => $item->quantity,
                    'unit_price' => $item->unit_price,
                ];
            }),
        ];
    }
}
