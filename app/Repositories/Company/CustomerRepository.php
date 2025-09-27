<?php

namespace App\Repositories\Company;

use App\Models\Company;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerRepository
{
    public function getAllCustomers(
        Company $company,
        $filters = [],
        $sort = 'desc',
        $paginate = true,
        $perPage = 10
    ): Collection|LengthAwarePaginator {
        $query = $company->customers()
            ->filters($filters)
            ->orderBy('id', $sort);

        if ($paginate) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    public function getCustomerById(Company $company, $id): ?Customer
    {
        return $company->customers()->find($id);
    }

    public function createCustomer(Company $company, array $data): ?Customer
    {
        return $company->customers()->create($data);
    }


    public function updateCustomer(Company $company, array $data, $id): ?Customer
    {
        $customer = $company->customers()->find($id);
        if (!$customer) {
            return null;
        }
        $customer->update($data);
        return $customer;
    }

    public function deleteCustomer(Company $company, $id): ?bool
    {
        $customer = $company->customers()->find($id);
        if (!$customer) {
            return null;
        }
        $customer->delete();
        return true;
    }
}
