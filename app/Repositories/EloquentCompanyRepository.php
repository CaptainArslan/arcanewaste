<?php

namespace App\Repositories;

use App\repositories\Contracts\CompanyRepositoryInterface;
use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;

class EloquentCompanyRepository implements CompanyRepositoryInterface
{
    public function getAllCompanies(): Collection
    {
        return Company::all();
    }

    public function getCompanyById($id): Company|null
    {
        return Company::find($id);
    }

    public function createCompany(array $data): Company
    {
        return Company::create($data);
    }

    public function updateCompany(array $data, $id): Company|null
    {
        return Company::find($id)->update($data);
    }

    public function deleteCompany($id): Company|null
    {
        return Company::find($id)->delete();
    }

    public function searchCompanies($query): Company|null|Collection
    {
        return Company::where('name', 'like', '%' . $query . '%')->get();
    }
}
