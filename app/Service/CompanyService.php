<?php

namespace App\Service;

use App\Repository\CompanyRepository;
use Illuminate\Support\Str;


class CompanyService
{
    private $repo;

    public function __construct(CompanyRepository $repo)
    {
        $this->repo = $repo;
    }

    public function listCompanies()
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