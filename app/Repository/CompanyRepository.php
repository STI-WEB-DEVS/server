<?php

namespace App\Repository;

use App\Models\Company;

class CompanyRepository
{
    public function all()
    {
        return Company::all();
    }

    public function find($id)
    {
        return Company::find($id);
    }

    public function create(array $payload)
    {
        return Company::create($payload);
    }

    public function update($id, array $payload)
    {
        $company = Company::find($id);
        if (!$company) return null;

        $company->update($payload);
        return $company;
    }

    public function delete($id)
    {
        $company = Company::find($id);
        if (!$company) return false;

        $company->delete();
        return true;
    }
}