<?php

namespace App\Repository;

use App\Models\Order;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderRepository
{
    protected $model;

    public function __construct(Order $model)
    {
        $this-> = $model;
    }

    public function paginate(int $perPage )
    {
        return $this-> model->paginate($perPage);
    }

    public function create(array $payload)
    {
        return $this->model->create($payload);
    }

    public function findByUuid(string $uuid)
    {
        return Order::where('uuid', $uuid)->firstOrFail();
    }

    public function findByWhere(array $criteria)
    {
        return $this->model->where($criteria)->get();
    }

    public function update(string $uuid, array $payload)
    {
        $model = $this->findByUuid($uuid);
        $model->update($payload);
        return $model;
    }

    public function delete(string $uuid)
    {
        $model = $this->findByUuid($uuid);
        return $model->delete();
    }

    public function restore(string $uuid)
    {
        $model = Order::withTrashed()->where('uuid', $uuid)->firstOrFail();
        $model->restore();
        return $model;
    }
}