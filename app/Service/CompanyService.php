<?php

namespace App\Service;

use App\Http\Resources\CompanyResource;
use App\Repository\CompanyRepository;

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
        return $this->repo->all();
    }

    public function createCompany(array $payload)
    {
        // Automatically generate UUID
        $payload['uuid'] = $this->generateUuid();

        return $this->repo->create($payload);
    }

    public function generateUiid()
    {
        
    }
    public function getCompanyById($id)
    {
        return $this->repo->find($id);
    }

    public function updateCompany($id, array $payload)
    {
        return $this->repo->update($id, $payload);
    }

    public function deleteCompany($id)
    {
        return $this->repo->delete($id);
    }
    public function generateUuid()
    {
        return (string) Str::uuid();
    }
}

