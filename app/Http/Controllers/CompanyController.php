<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyStoreRequest;
use App\Service\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CompanyController extends Controller
{
    private CompanyService $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function index(Request $request)
    {
        return $this->companyService->listCompany($request->input('per_page', 15));
    }

    public function store(CompanyStoreRequest $request)
    {
        return $this->companyService->createCompany($request->all());
    }

    public function show(string $uuid)
    {
        return $this->companyService->getCompany($uuid);
    }

    public function update(Request $request, string $uuid)
    {
        return $this->companyService->updateCompany($uuid, $request->all());
    }

    public function destroy(string $uuid)
    {
        $this->companyService->deleteCompany($uuid);
        return response()->json(['message' => 'Deleted successfully'], 200);
    }
    
    public function restore(string $uuid)
    {
        return $this->companyService->restoreCompany($uuid);
    }
}