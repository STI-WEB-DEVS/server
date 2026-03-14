<?php

namespace App\Service;

use App\Repository\CompanyRepository;
use App\Http\Resources\CompanyResource;

class CompanyService
{
    private CompanyRepository $companyRepository;

    public function __construct(CompanyRepository $companyRepository) 
    {
        $this->companyRepository = $companyRepository;
    }

    public function listCompany(int $perPage = 15)
    {
        $collection = $this->companyRepository->paginate($perPage);
        return CompanyResource::collection($collection);
    }

    public function createCompany(array $payload)
    {
        $model = $this->companyRepository->create($payload);
        return new CompanyResource($model);
    }

    public function getCompany(string $uuid)
    {
        $model = $this->companyRepository->findByUuid($uuid);
        return new CompanyResource($model);
    }

    public function getCompanyByField(string $field, $value)
    {
        $model = $this->companyRepository->findByField($field, $value);
        return new CompanyResource($model);
    }

    public function updateCompany(string $uuid, array $payload)
    {
        $model = $this->companyRepository->update($uuid, $payload);
        return new CompanyResource($model);
    }

    public function deleteCompany(string $uuid)
    {
        $this->companyRepository->delete($uuid);
        return true;
    }

    public function restoreCompany(string $uuid)
    {
        $model = $this->companyRepository->restore($uuid);
        return new CompanyResource($model);
    }
}