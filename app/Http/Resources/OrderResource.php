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
        'uuid' => $this->uuid,

        'customer' => [
            'uuid' => $this->customer->uuid,
            'name' => $this->customer->name,
            'email' => $this->customer->email,
        ],

        'total_amount' => number_format($this->total_amount, 2),

        'items' => OrderItemResource::collection($this->items),

        'created_at' => $this->created_at
    ];
    }
}