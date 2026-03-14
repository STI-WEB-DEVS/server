<?php

namespace App\Repository;

use App\Models\Company;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CompanyRepository
{
    public function paginate(int $perPage = 15)
    {
        return Company::latest()->paginate($perPage);
    }

    public function create(array $payload)
    {
        return Company::create($payload);
    }

    public function findByUuid(string $uuid)
    {
        return Company::where('uuid', $uuid)->firstOrFail();
    }

    public function findByField(string $field, $value)
    {
        return Company::where($field, $value)->firstOrFail();
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
        $model = Company::withTrashed()->where('uuid', $uuid)->firstOrFail();
        $model->restore();
        return $model;
    }
}