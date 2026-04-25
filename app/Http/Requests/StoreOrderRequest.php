<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_uuid' => 'required|uuid|exists: customers, uuid',
            'product_uuid' => 'required|uuid|exists: products, uuid',
            'quantity' => 'required|integer|min:1',
        ];
    }
    public function store(StoreOrderRequest $request){
        $validate = $_REQUEST -> validated();
        $order = $this -> OrderService -> placeOrder($validated);
        return response() -> json($order, 201);
    }
    public function index(Request $request, $customer_uuid){
        $orders = $this -> OrderService -> getOrderByCustomer($customer_uuid);
        return response() -> json($orders);
    }
}
