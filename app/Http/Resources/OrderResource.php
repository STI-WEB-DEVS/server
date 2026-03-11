<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *

     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'order_id'          => $this->id,
            'order_uuid'        => $this->uuid,
            'customer_name'     => $this->customer->name,
            'customer_id'       => $this->customer_id,
            'order_items'       => $this->items->map(function($item) {
                return [
                    'order_item_id'     => $item->id,
                    'product_id'        => $item->product_id,
                    'product_name'      => $item->product->name,
                    'quantity'          => $item->quantity,
                    'unit_price'        => $item->unit_price,
                    'item_total'        => $item->quantity * $item->unit_price,
                ];
            }),
            'total_amount' => $this->total_amount,
        ];
    }
}
