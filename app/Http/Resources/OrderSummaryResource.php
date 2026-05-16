<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'total_revenue' => $this['total_revenue'],
            'customer_count' => $this['customer_count'],
            'order_count' => $this['order_count'],
            'top_products' => $this['top_products'],
            'recent_orders' => $this['recent_orders'],
        ];
    }
}