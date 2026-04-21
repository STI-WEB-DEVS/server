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
            // Use the relationship to get the Customer's UUID string instead of the ID number
            'customer_uuid'  => $this->customer->uuid ?? $this->customer_id, 
            'total_amount'   => $this->total_amount,
            'created_at'     => $this->created_at->format('Y-m-d H:i:s'),
            
            // This pulls the data from the order_items table
            'items' => $this->whenLoaded('items', function() {
                return $this->items->map(function($item) {
                    return [
                        // Use the product relationship to get the Product's UUID string
                        'product_uuid' => $item->product->uuid ?? $item->product_id,
                        'quantity'     => $item->quantity,
                        'unit_price'   => $item->unit_price,
                    ];
                });
            }),
        ];
    }
}