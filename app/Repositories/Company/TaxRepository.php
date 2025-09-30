<?php

namespace App\Repositories\Company;

use App\Models\Company;
use App\Models\Tax;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TaxRepository
{
    public function getAllTaxes(Company $company, $filters = [], $sort = 'desc', $paginate = true, $perPage = 10): Collection|LengthAwarePaginator
    {
        $query = $company->taxes()
            ->filters($filters)
            ->orderBy('id', $sort);

        if ($paginate) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    public function getTaxById(Company $company, $id): ?Tax
    {
        return $company->taxes()->find($id);
    }

    public function createTax(Company $company, array $data): ?Tax
    {
        return $company->taxes()->create($data);
    }

    public function updateTax(Company $company, array $data, $id): ?Tax
    {
        $tax = $company->taxes()->find($id);
        if (! $tax) {
            return null;
        }
        $tax->update($data);

        return $tax;
    }

    public function deleteTax(Company $company, $id): ?bool
    {
        $tax = $company->taxes()->find($id);
        if (! $tax) {
            return null;
        }

        if ($tax->dumpsterSizes()->exists()) {
            throw new \Exception('Tax has dumpsters associated with it. please move the dumpsters to another tax before deleting this tax');
        }

        return $tax->delete();
    }
}
