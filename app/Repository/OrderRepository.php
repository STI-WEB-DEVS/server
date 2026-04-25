<?php

namespace App\Repository;

use App\Models\Order;
// Use your BaseRepository if the scaffold created one
// use App\Repository\BaseRepository; 

class OrderRepository
{
    protected $model;

    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    /**
     * Standard create method
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * This is the method the Service calls to get a customer's orders
     */
    public function findWhere(array $criteria)
    {
        return $this->model->where($criteria)->get();
    }

    /**
     * Standard pagination for the index
     */
    public function paginate(int $perPage)
    {
        return $this->model->paginate($perPage);
    }

    // ... Other methods like findByUuid, update, delete ...
}