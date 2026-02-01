<?php



namespace App\Service;



use App\Models\Company;

use Illuminate\Http\Request;

use Illuminate\Validation\ValidationException;



class CompanyService

{

    public function listCompanies()

    {

        return Company::latest()->get();           // or ->paginate(20)

    }



    public function createCompany(Request $request)

    {

        $validated = $request->validate([

            'name' => 'required|string|min:2|max:180|unique:companies,name',

            // add more rules later

        ]);



        return Company::create($validated);

    }



    public function getCompany(string $id): Company

    {

        return Company::findOrFail($id);

        // or Company::where('uuid', $id)->firstOrFail(); if using uuid in route

    }



    public function updateCompany(Request $request, string $id)

    {

        $company = $this->getCompany($id);



        $validated = $request->validate([

            'name' => 'sometimes|required|string|min:2|max:180|unique:companies,name,' . $company->id,

        ]);



        $company->update($validated);



        return $company->fresh();

    }



    public function deleteCompany(string $id)

    {

        $company = $this->getCompany($id);

        $company->delete();

        // or $company->forceDelete() if you want permanent delete

    }

}
