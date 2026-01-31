<?php

namespace App\Http\Controllers;

use App\Service\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    private $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function index()
    {
        return $this->companyService->listCompanies();
    }

    public function store(Request $request)
    {
        $payload = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        return response()->json($this->companyService->createCompany($payload), 201);
    }

    public function show(string $id)
    {
        $company = $this->companyService->getCompanyById($id);

        return $company
            ? response()->json($company)
            : response()->json(['message' => 'Company not found'], 404);
    }

    public function update(Request $request, string $id)
    {
        $payload = $request->validate([
            'name' => 'sometimes|string|max:255',
        ]);

        $updated = $this->companyService->updateCompany($id, $payload);

        return $updated
            ? response()->json($updated)
            : response()->json(['message' => 'Company not found'], 404);
    }

    public function destroy(string $id)
    {
        $deleted = $this->companyService->deleteCompany($id);

        return $deleted
            ? response()->json(['message' => 'Company deleted'])
            : response()->json(['message' => 'Company not found'], 404);
    }
}
