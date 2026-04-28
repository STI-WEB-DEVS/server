<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'uuid'          => $this->uuid,
            'customer_uuid' => $this->customer->uuid ?? null, // Assuming order has a customer relationship
            'total_amount'  => $this->total_amount,
            'created_at'    => $this->created_at->format('Y-m-d H:i:s'),
            
            'items' => $this->items->map(function($item) {
                return [
                    'product_uuid' => $item->product->uuid ?? null, // Assuming item has product relationship
                    'product_name' => $item->product->name ?? 'Unknown Product', // <--- Add this
                    'quantity'     => $item->quantity,
                    'unit_price'   => $item->unit_price,
                ];
            }),
        ];
    }
}