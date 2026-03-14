<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid'         => $this->uuid,
            'total_amount' => $this->total_amount,
            'created_at'   => $this->created_at?->toDateTimeString(),
            'updated_at'   => $this->updated_at?->toDateTimeString(),
            // customer — only when loaded
            'customer'     => new CustomerResource(
                                  $this->whenLoaded('customer')
                              ),
            // items — only when loaded
            'items'        => OrderItemResource::collection(
                                  $this->whenLoaded('items')
                              ),
        ];
    }
}