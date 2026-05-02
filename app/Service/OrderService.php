<?php

namespace App\Service;


use Illuminate\Support\Facades\DB;
use App\Repository\OrderRepository;
use App\Repository\CustomerRepository;
use App\Repository\ProductRepository;
use App\Http\Resources\OrderResource;

class OrderService
{
    private OrderRepository $orderRepository;
    private CustomerRepository $customerRepository;
    private ProductRepository  $productRepository;
    

    public function __construct(OrderRepository $orderRepository, CustomerRepository $customerRepository, ProductRepository  $productRepository) 
    {
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
    }

    public function listOrder(int $perPage = 15)
    {
        $collection = $this->orderRepository->paginate($perPage);
        return OrderResource::collection($collection);
    }

    public function createOrder(array $payload)
    {
        return DB::transaction(function () use ($payload) {
            $total_amount = 0;
            $customer = $this->customerRepository->findByUuid($payload['customer_uuid']);
        
    
            $orderHeader = $this->orderRepository->create([
                'customer_id'  => $customer->id,
                'total_amount' => 0, 
            ]);
        
            foreach ($payload['orders'] as $item) {
                $product = $this->productRepository->findByUuid($item['product_uuid']);
                
                $lineTotal = $product->price * $item['quantity'];
                $total_amount += $lineTotal;
        
          
                $orderHeader->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $product->price,
                ]);
            }
        
  
            $orderHeader->update(['total_amount' => $total_amount]);
        
         
            return new OrderResource($orderHeader->load('items'));
        });



    }

    public function getOrder(string $uuid)
    {
        $model = $this->orderRepository->findByUuid($uuid);
        return new OrderResource($model);
    }

    public function getOrderByField(string $field, $value)
    {
        $model = $this->orderRepository->findByField($field, $value);
        return new OrderResource($model);
    }

    public function updateOrder(string $uuid, array $payload)
    {
        $model = $this->orderRepository->update($uuid, $payload);
        return new OrderResource($model);
    }

    public function deleteOrder(string $uuid)
    {
        $this->orderRepository->delete($uuid);
        return true;
    }

    public function restoreOrder(string $uuid)
    {
        $model = $this->orderRepository->restore($uuid);
        return new OrderResource($model);
    }
}