<?php

namespace App\Repositories\Company;

use App\Models\Company;
use App\Models\DumpsterSize;
use App\Models\Tax;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class DumpsterSizeRepository
{
    public function getAllDumpsterSizes(
        Company $company,
        $filters = [],
        $sort = 'desc',
        $paginate = true,
        $perPage = 10
    ): Collection|LengthAwarePaginator {
        $query = $company->dumpsterSizes()
            ->with(['taxes', 'promotions', 'company'])
            ->filters($filters)
            ->orderBy('id', $sort);

        if ($paginate) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    public function getDumpsterSizeById(Company $company, $id): ?DumpsterSize
    {
        return $company->dumpsterSizes()
            ->with(['taxes', 'promotions', 'company'])
            ->where('id', $id)
            ->first(); // returns single DumpsterSize or null
    }

    public function createDumpsterSize(Company $company, array $data): ?DumpsterSize
    {
        $dumpsterSize = $company->dumpsterSizes()->create($data);
        if (isset($data['taxes'])) {
            $this->attachTaxes($dumpsterSize, $data['taxes']);
        }

        return $dumpsterSize;
    }

    public function updateDumpsterSize(Company $company, array $data, $id): ?DumpsterSize
    {
        $dumpsterSize = $company->dumpsterSizes()->find($id);
        if (! $dumpsterSize) {
            return null;
        }
        $dumpsterSize->update($data);
        if (isset($data['taxes'])) {
            $this->attachTaxes($dumpsterSize, $data['taxes']);
        }

        return $dumpsterSize;
    }

    public function deleteDumpsterSize(Company $company, $id): ?bool
    {
        $dumpsterSize = $company->dumpsterSizes()->find($id);
        if (! $dumpsterSize) {
            return null;
        }

        if (
            // $dumpsterSize->dumpsters()->exists()
            // ||
            $dumpsterSize->promotions()->exists()
            || $dumpsterSize->taxes()->exists()
            || $dumpsterSize->company()->exists()
        ) {
            throw new \Exception('Dumpster size has promotions associated with it. please remove the promotions before deleting this dumpster size');
        }

        $dumpsterSize->taxes()->detach();
        $dumpsterSize->promotions()->detach();

        return $dumpsterSize->delete();
    }

    public function attachTaxes(DumpsterSize $dumpsterSize, array $taxes = []): ?DumpsterSize
    {
        // Get all active taxes by IDs
        $activeTaxes = Tax::whereIn('id', $taxes)
            ->where('is_active', 1)
            ->pluck('id')
            ->toArray();

        // Check if any requested tax is inactive or does not exist
        $missingTaxes = array_diff($taxes, $activeTaxes);
        if (! empty($missingTaxes)) {
            throw new \Exception('All tax IDs must be active. Invalid or inactive IDs: '.implode(', ', $missingTaxes));
        }

        // Sync the active taxes with the dumpster size
        $dumpsterSize->taxes()->sync($activeTaxes);

        return $dumpsterSize;
    }
}
