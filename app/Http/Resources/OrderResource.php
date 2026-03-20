<?php

namespace App\Http\Resources;

use App\Http\Resources\OrderItemResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid'         => $this->uuid,
            'customer'     => [
                'uuid'  => $this->customer->uuid ?? null,
                'name'  => $this->customer->name ?? null,
                'email' => $this->customer->email ?? null,
            ],
            'total_amount' => $this->total_amount,
            'items'        => OrderItemResource::collection($this->whenLoaded('items')),
            'created_at'   => $this->created_at,
        ];
    }
}