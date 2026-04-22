<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid'           => $this->uuid,
            'customer_uuid'  => $this->customer->uuid ?? $this->customer_id,
            'total_amount'   => $this->total_amount,
            'created_at'     => $this->created_at->format('Y-m-d H:i:s'),

            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(function ($item) {
                    return [
                        'product_uuid' => $item->product->uuid ?? $item->product_id,
                        'quantity'     => $item->quantity,
                        'unit_price'   => $item->unit_price,
                    ];
                });
            }),
        ];
    }
}
