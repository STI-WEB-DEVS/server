<?php

namespace App\Service;

use Illuminate\Support\Facades\DB;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\CustomerRepository;
use App\Http\Resources\OrderResource;

class OrderService
{
    private OrderRepository $orderRepository;
    private ProductRepository $productRepository;
    private CustomerRepository $customerRepository;

    public function __construct(OrderRepository $orderRepository, ProductRepository $productRepository, CustomerRepository $customerRepository) 
    {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
    }

    public function listOrder(int $perPage = 15)
    {
        $collection = $this->orderRepository->paginate($perPage);
        return OrderResource::collection($collection);
    }


    public function createOrder(array $payload)
    {
        return DB::transaction(function () use ($payload) {
            $orders = null;
            $total_amount = 0;
            $customer_uuid = $payload['customer_uuid'];
            foreach ($payload['orders'] as $order) {
                //PAYLOADS
                $quantity = $order['quantity'];
                $product_uuid = $order['product_uuid'];

                // CUSTOMER ID
                $customer = $this->customerRepository->retrieveCustomer($customer_uuid);


                //CREATE PRODUCT
                $product = $this->productRepository->findByUuid($product_uuid);
                $price = $product->price;
                $product_id = $product->id;
                $total = $price * $quantity;

                $orderData = [
                    'customer_id' =>  $customer->id,
                ];
        

                if (!$orders){
                    $total_amount = $total;
                    $orders = $this->orderRepository->create($orderData);
                } else {
                    $total_amount = $total + $total_amount;
                }
                
                DB::insert('insert into order_items (order_id, product_id, quantity, unit_price)
                    values (?, ?, ?, ?)', [$orders->id,  $product_id, $quantity, $price]);

            };
            $orders->total_amount = $total_amount;
            $orders->save();
          
            return new OrderResource($orders);
            });
        }
        

      
    public function getOrder(string $uuid)
    {
        // $customer = $this->customerRepository->findByUuid($uuid);
        // // $model = $this->getOrderByField('customer_id', $customer->u);
        // $down = DB::select('select * from order_items WHERE order_id = (?)', [$model->id]);
        // $data = [
        //     'order' => $model,
        //     'order_item' => $down
        // ];
        // return  $data;
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