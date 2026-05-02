<?php

namespace App\Service;

use App\Repository\OrderRepository;
use Illuminate\Support\Str;

class OrderService
{
    protected $orderRepo;

    public function __construct(OrderRepository $orderRepo)
    {
        $this->orderRepo = $orderRepo;
    }

    public function createOrder(array $data)
    {
        // 1. Hanapin ang Customer
        $customer = \App\Models\Customer::where('uuid', $data['customer_uuid'])->firstOrFail();
        
        // 2. Hanapin ang Product para makuha ang presyo
        $product = \App\Models\Product::where('uuid', $data['product_uuid'])->firstOrFail();
    
        // 3. Siguraduhin natin na ang 'price' ay column sa products table mo.
        // Dito natin lulutuin ang calculation.
        $calculatedTotal = (float) $product->pr ice * (int) $data['quantity'];
    
        // 4. BUUIN ang bagong array. Huwag i-rely sa original na $data lang.
        $payload = [
            'uuid'          => (string) \Illuminate\Support\Str::uuid(),
            'customer_id'   => $customer->id,
            'quantity'      => $data['quantity'],
            'total_amount'  => $calculatedTotal, // <--- Ito ang pinaka-importante
        ];
    
        // 5. I-pasa ang $payload sa repo
        return $this->orderRepo->create($payload);
    }

public function getOrdersByCustomer($customerUuid)
{
    // 1. Hanapin muna ang Customer record gamit ang UUID
    // para makuha natin ang numeric ID nito.
    $customer = \App\Models\Customer::where('uuid', $customerUuid)->firstOrFail();

    // 2. Ngayon, gamitin ang numeric ID ($customer->id) para i-filter ang orders.
    // Ito ang itutugma natin sa 'customer_id' column ng orders table.
    return $this->orderRepo->findWhere(['customer_id' => $customer->id]);
}

    
}