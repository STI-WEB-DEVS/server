<?php

namespace App\Http\Controllers;

use App\Service\CompanyService;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    private $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    // GET /companies
    public function index()
    {
        return response()->json($this->companyService->listCompanies());
    }

    // POST /companies
    public function store(Request $request)
    {
        // Only validate 'name' since uuid is auto-generated
        $payload = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        return response()->json($this->companyService->createCompany($payload), 201);
    }

    // GET /companies/{id}
    public function show(string $id)
    {
        $company = $this->companyService->getCompanyById($id);

        return $company
            ? response()->json($company)
            : response()->json(['message' => 'Company not found'], 404);
    }

    // PUT /companies/{id}
    public function update(Request $request, string $id)
    {
        // Allow updating only 'name'
        $payload = $request->validate([
            'name' => 'sometimes|string|max:255',
        ]);

        $updated = $this->companyService->updateCompany($id, $payload);

        return $updated
            ? response()->json($updated)
            : response()->json(['message' => 'Company not found'], 404);
    }

    // DELETE /companies/{id}
    public function destroy(string $id)
    {
        $deleted = $this->companyService->deleteCompany($id);

        return $deleted
            ? response()->json(['message' => 'Company deleted'])
            : response()->json(['message' => 'Company not found'], 404);
    }
}
