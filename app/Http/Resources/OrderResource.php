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
            'uuid'         => $this->uuid,
            'total_amount' => (float) $this->total_amount,
            'created_at'   => $this->created_at,
            'items'        => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}

