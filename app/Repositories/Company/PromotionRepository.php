<?php

namespace App\Repositories\Company;

use App\Models\Company;
use App\Models\Promotion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PromotionRepository
{
    public function getAllPromotions(
        Company $company,
        $filters = [],
        $sort = 'desc',
        $paginate = true,
        $perPage = 10
    ): Collection|LengthAwarePaginator {
        $query = $company->promotions()
            ->filters($filters)
            ->orderBy('id', $sort);

        if ($paginate) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    public function getPromotionById(Company $company, $id): ?Promotion
    {
        $promotion = $company->promotions()
            ->with(['dumpsterSizes'])
            ->find($id);

        if (! $promotion) {
            return null;
        }

        return $promotion;
    }

    public function createPromotion(Company $company, array $data): ?Promotion
    {
        foreach ($data['dumpster_size_ids'] as $dumpsterSizeId) {
            if ($this->checkPromotionOverlap($company, $data, $dumpsterSizeId)) {
                throw new \Exception("Promotion already exists for dumpster size {$dumpsterSizeId} in the selected date range.");
            }
        }

        $promotion = $company->promotions()->create($data);
        $promotion->dumpsterSizes()->attach($data['dumpster_size_ids']);
        return $promotion;
    }

    public function updatePromotion(Company $company, array $data, $promotionId): ?Promotion
    {
        $promotion = $company->promotions()->find($promotionId);
        if (! $promotion) {
            return null;
        }

        foreach ($data['dumpster_size_ids'] as $dumpsterSizeId) {
            if ($this->checkPromotionOverlap($company, $data, $dumpsterSizeId, $promotionId)) {
                throw new \Exception('Promotion has overlap with another promotion');
            }
        }
        $promotion->update($data);
        return $promotion;
    }

    public function deletePromotion(Company $company, $id): ?bool
    {
        $promotion = $company->promotions()->find($id);
        if (! $promotion) {
            return null;
        }

        if ($this->checkPromotionHasOrders($company, $promotion->id)) {
            throw new \Exception('Promotion has orders associated with it. please remove the orders before deleting this promotion');
        }

        $promotion->dumpsterSizes()->detach();

        $promotion->delete();
        return true;
    }

    public function checkPromotionHasOrders(Company $company, $promotionId): ?bool
    {
        return $company->orders()
            ->whereHas('discounts', function ($query) use ($promotionId) {
                $query->where('promotion_id', $promotionId);
            })
            ->exists();
    }


    public function checkPromotionOverlap(Company $company, array $data, $dumpsterSizeId, $id = null): ?bool
    {
        if (isset($dumpsterSizeId)) {
            return $this->hasOverlap($company, $dumpsterSizeId, $data['start_date'], $data['end_date'], $id);
        }
        return false;
    }

    public function hasOverlap(
        Company $company,
        int $dumpsterId,
        string $startDate,
        string $endDate,
        int $ignorePromotionId = null
    ): bool {
        return $company->promotions()
            ->whereHas(
                'dumpsterSizes',
                fn(Builder $q) =>
                $q->where('dumpster_size_id', $dumpsterId)
            )
            ->when(
                $ignorePromotionId,
                fn(Builder $q) =>
                $q->where('id', '!=', $ignorePromotionId)
            )
            ->where(function (Builder $q) use ($startDate, $endDate) {
                // Check if start date falls within another promotion's date range
                $q->whereBetween('start_date', [$startDate, $endDate])
                    // Check if end date falls within another promotion's date range  
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    // Check if another promotion completely encompasses this date range
                    ->orWhere(
                        fn(Builder $q2) =>
                        $q2->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate)
                    );
            })
            ->exists();
    }
}
