<?php

namespace App\Repository;

use App\Models\Customer;

class CustomerRepository
{
    public function createCustomer($payload){
        return Customer::create($payload);
    }


public function getAllCustomers(){
    return Customer::latest()->paginate(5);
}
    
 
    public function retrieveCustomer($payload){

        return Customer::where('uuid',$payload)->first();
    }
 
    public function updateCustomer($payload,$id){

        $customer = $this->retrieveCustomer($id);
        $customer->update($payload);
        $customer->save();
        return $customer;
    }
    public function deleteCustomer($id){
        $customer = $this->retrieveCustomer($id);
        $customer->delete();
        return response()->json([
            'message' => "Customer has been deleted"
        ], 200);
        return "Customer has been deleted";
    }
}
