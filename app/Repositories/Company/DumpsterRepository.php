<?php

namespace App\Repositories\Company;

use App\Models\Company;
use App\Models\Dumpster;
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
        return $dumpster->update($data);
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
}
