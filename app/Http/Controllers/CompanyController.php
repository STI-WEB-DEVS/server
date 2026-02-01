<?php



namespace App\Http\Controllers;



use App\Service\CompanyService;

use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;

use Illuminate\Validation\ValidationException;



class CompanyController extends Controller

{

    private CompanyService $companyService;



    public function __construct(CompanyService $companyService)

    {

        $this->companyService = $companyService;

    }



    public function index(): JsonResponse

    {

        return response()->json($this->companyService->listCompanies());

    }



    public function store(Request $request): JsonResponse

    {

        try {

            $company = $this->companyService->createCompany($request);

            return response()->json($company, 201);

        } catch (ValidationException $e) {

            return response()->json(['errors' => $e->errors()], 422);

        } catch (\Exception $e) {

            return response()->json(['message' => 'Server error'], 500);

        }

    }



    public function show(string $id): JsonResponse

    {

        try {

            $company = $this->companyService->getCompany($id);

            return response()->json($company);

        } catch (\Exception $e) {

            return response()->json(['message' => 'Company not found'], 404);

        }

    }



    public function update(Request $request, string $id): JsonResponse

    {

        try {

            $company = $this->companyService->updateCompany($request, $id);

            return response()->json($company);

        } catch (ValidationException $e) {

            return response()->json(['errors' => $e->errors()], 422);

        } catch (\Exception $e) {

            return response()->json(['message' => $e->getMessage()], 404);

        }

    }



    public function destroy(string $id): JsonResponse

    {

        try {

            $this->companyService->deleteCompany($id);

            return response()->json(['message' => 'Company deleted'], 200);

        } catch (\Exception $e) {

            return response()->json(['message' => 'Company not found'], 404);

        }

    }

}
