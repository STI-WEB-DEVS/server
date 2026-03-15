<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrdersResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'uuid'       => $this->uuid,
            'customer'   => [
                'uuid'  => $this->customer->uuid ?? null,
                'name'  => $this->customer->name ?? null,
                'email' => $this->customer->email ?? null,
            ],
            'items' => collect($this->orderItems ?? [])->map(function ($item) {
                return [
                    'product_uuid' => $item->product->uuid ?? null,
                    'name'         => $item->product->name ?? null,
                    'quantity'     => $item->quantity,
                ];
            }),
            
            'total_amt' => collect($this->orderItems ?? [])->sum(fn($item) => $item->quantity * ($item->product->price ?? 0)),

            'created_at' => $this->created_at,
        ];
    }

    private function calculateTotalAmount()
    {
        return $this->products->sum(function ($product) {
            return $product->pivot->quantity * $product->price;
        });
    }
    
}