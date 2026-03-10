<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'order_id' => $this->id,
            'uuid' => $this->uuid,
            'customer_name' => $this->customer?->name,
            'customer_id' => $this->customer_id,
            'order_items' => OrderItemResource::collection($this->whenLoaded('items')),
            'total_amount' => $this->items->sum(function ($item) {
                return $item->quantity * $item->unit_price;
            }),
        ];
    }
}