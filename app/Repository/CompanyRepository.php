<?php

namespace App\Repository;

use App\Models\Company;

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
        $company = $this->findByUuid($uuid);
        $company->update($payload);

        return $company;
    }

    public function delete(string $uuid)
    {
        $company = $this->findByUuid($uuid);

        return $company->delete();
    }

    public function restore(string $uuid)
    {
        $company = Company::withTrashed()
            ->where('uuid', $uuid)
            ->firstOrFail();

        $company->restore();

        return $company;
    }
}