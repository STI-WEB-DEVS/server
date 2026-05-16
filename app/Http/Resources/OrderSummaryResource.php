<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'from' => $this['from'],
            'to' => $this['to'],

            'summary' => [
                'total_sales' => $this['total_sales'],
                'total_customers' => $this['total_customers'],
            ],

            'top_products' => $this['top_products'],
            'daily_sales_trend' => $this['daily_sales_trend'],
        ];
    }
}
