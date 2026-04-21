<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'customer_uuid' => $this->customer?->uuid,
            'total_amount' => (float) $this->total_amount,
            'items' => $this->items->map(function ($item) {
                return [
                    'product_uuid' => $item->product?->uuid,
                    'quantity' => (int) $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'line_total' => (float) ($item->unit_price * $item->quantity),
                ];
            })->values(),
            'created_at' => $this->created_at,
        ];
    }
}