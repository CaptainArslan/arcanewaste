<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class WarehouseRepository
{
    public function getAllWarehouses(
        Company $company,
        $filters = [],
        $sort = 'desc',
        $paginate = true,
        $perPage = 10
    ): Collection|LengthAwarePaginator {
        $query = $company->warehouses()
            ->filters($filters)
            ->orderBy('id', $sort);

        if ($paginate) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    public function getWarehouseById(Company $company, $id): ?Warehouse
    {
        return $company->warehouses()->find($id);
    }

    public function createWarehouse(Company $company, array $data): ?Warehouse
    {
        $address = $data['address'];
        $warehouse = $company->warehouses()->create($data);
        $warehouse->createAddress($warehouse, $address, true);

        return $warehouse;
    }

    public function updateWarehouse(Company $company, $id, array $data): ?Warehouse
    {
        $warehouse = $company->warehouses()->find($id);
        if (! $warehouse) {
            return null;
        }

        $warehouse->update([
            'name' => $data['name'],
            'code' => $data['code'],
            'type' => $data['type'],
            'capacity' => $data['capacity'],
            'is_active' => $data['is_active'],
        ]);

        if (isset($data['address'])) {
            $warehouse->updateAddress($warehouse, $data['address'], true);
        }

        return $warehouse;
    }

    public function deleteWarehouse(Company $company, $id): ?Warehouse
    {
        $warehouse = $company->warehouses()->find($id);
        if (! $warehouse) {
            return null;
        }
        if ($warehouse->dumpsters()->exists()) {
            throw new \Exception('Warehouse has dumpsters associated with it. please move the dumpsters to another warehouse before deleting this warehouse');
        }
        $warehouse->delete();
        $warehouse->addresses()->delete();
        $warehouse->timings()->delete();
        $warehouse->documents()->delete();

        return $warehouse;
    }
}
