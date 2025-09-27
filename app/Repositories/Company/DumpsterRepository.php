<?php

namespace App\Repositories\Company;

use App\Models\Company;
use App\Models\Dumpster;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class DumpsterRepository
{
    public function getAllDumpsters(
        Company $company,
        $filters = [],
        $sort = 'desc',
        $paginate = true,
        $perPage = 10
    ): Collection|LengthAwarePaginator {
        $query = $company->dumpsters()
            ->filters($filters)
            ->orderBy('id', $sort);

        if ($paginate) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    public function getDumpsterById(Company $company, $id): ?Dumpster
    {
        return $company->dumpsters()->find($id);
    }

    public function createDumpster(Company $company, array $data): ?Dumpster
    {
        return $company->dumpsters()->create($data);
    }

    public function updateDumpster(Company $company, $id, array $data): ?Dumpster
    {
        $dumpster = $company->dumpsters()->find($id);
        if (!$dumpster) {
            return null;
        }
        $dumpster->update($data);
        return $dumpster;
    }

    public function deleteDumpster(Company $company, $id): ?bool
    {
        $dumpster = $company->dumpsters()->find($id);
        if (!$dumpster) {
            return null;
        }
        $dumpster->delete();
        return true;
    }

    public function attachTaxes(Dumpster $dumpster, array $taxes): ?Dumpster
    {
        $dumpster->taxes()->attach($taxes);
        return $dumpster;
    }

    public function detachTaxes(Dumpster $dumpster): ?Dumpster
    {
        $dumpster->taxes()->detach();
        return $dumpster;
    }

    public function attachPromotions(Dumpster $dumpster, array $promotions): ?Dumpster
    {
        $dumpster->promotions()->attach($promotions);
        return $dumpster;
    }

    public function detachPromotions(Dumpster $dumpster): ?Dumpster
    {
        $dumpster->promotions()->detach();
        return $dumpster;
    }

    public function attachWarehouse(Dumpster $dumpster, Warehouse $warehouse): ?Dumpster
    {
        $dumpster->warehouse()->associate($warehouse);
        return $dumpster;
    }

    public function detachWarehouse(Dumpster $dumpster): ?Dumpster
    {
        $dumpster->warehouse()->dissociate();
        return $dumpster;
    }
}
