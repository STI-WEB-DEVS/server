<?php



namespace App\Repositories;



use App\Models\Company;

use Illuminate\Database\Eloquent\Collection;

use Illuminate\Database\Eloquent\ModelNotFoundException;



class CompanyRepository

{

    protected Company $model;



    public function __construct(Company $model)

    {

        $this->model = $model;

    }



    /**

     * Get all companies (with optional pagination)

     */

    public function getAll(bool $paginate = false, int $perPage = 15): Collection|\Illuminate\Pagination\LengthAwarePaginator

    {

        $query = $this->model->latest();



        return $paginate

            ? $query->paginate($perPage)

            : $query->get();

    }



    /**

     * Find company by ID (or UUID if you switch route key)

     *

     * @throws ModelNotFoundException

     */

    public function findOrFail(string|int $id): Company

    {

        return $this->model->findOrFail($id);



        // If you use UUID in URLs instead of ID:

        // return $this->model->where('uuid', $id)->firstOrFail();

    }



    /**

     * Create a new company

     */

    public function create(array $data): Company

    {

        return $this->model->create($data);

    }



    /**

     * Update existing company

     */

    public function update(Company $company, array $data): bool

    {

        return $company->update($data);

    }



    /**

     * Delete company

     */

    public function delete(Company $company): bool

    {

        return $company->delete();

    }



    // Optional: example of more specific method

    // public function findByName(string $name): ?Company

    // {

    //     return $this->model->where('name', 'like', "%{$name}%")->first();

    // }

}