<?php

namespace App\Repositories\Company;

use App\Models\Promotion;
use App\Models\Company;
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
    
    public function getPromotionById(Company $company, $id): ?Promotion {
        return $company->promotions()->find($id);
    }
    
    public function createPromotion(Company $company, array $data): ?Promotion {
        return $company->promotions()->create($data);
    }
    
    public function updatePromotion(Company $company, array $data, $id): ?Promotion {
        $promotion = $company->promotions()->find($id);
        if (! $promotion) {
            return null;
        }
        $promotion->update($data);
        return $promotion;
    }
    
    public function deletePromotion(Company $company, $id): ?bool {
        $promotion = $company->promotions()->find($id);
        if (! $promotion) {
            return null;
        }
        $promotion->delete();
        return true;
    }
}
