<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'uuid'          => $this->uuid,
            'order_uuid'    => $this->uuid, // alias for frontend
            'customer_uuid' => $this->customer->uuid, // expose UUID for filtering
            'customer'      => [
                'name'  => $this->customer->name,
                'email' => $this->customer->email,
            ],
            'items'         => OrderItemResource::collection($this->items),
            'total_price'   => $this->total_amount,
            'status'        => $this->status,
            'created_at'    => $this->created_at->toDateTimeString(),
            'updated_at'    => $this->updated_at->toDateTimeString(),
        ];
    }
}
