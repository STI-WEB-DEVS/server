<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'order_uuid'    => $this->uuid,
            'status'        => $this->status,
            'grand_total'   => $this->total_amount,
            'date'          => $this->created_at->format('Y-m-d H:i:s'),
            
            'order_items'   => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
