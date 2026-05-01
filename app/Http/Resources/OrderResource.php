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
            'customer_id' => $this->customer_id,
            'id' => $this->id,
            'uuid' => $this->uuid,
            'total_amount' => $this->total_amount,
            
            'items' => $this->whenLoaded('items'), 
            
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}