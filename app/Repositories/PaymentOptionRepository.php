<?php

namespace App\Repositories;

use App\Enums\PaymentOptionTypeEnum;
use App\Models\Company;
use App\Models\PaymentOption;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentOptionRepository
{
    public function getAllPaymentOptions(
        Company $company,
        $filters = [],
        $sort = 'desc',
        $paginate = true,
        $perPage = 10
    ): Collection|LengthAwarePaginator {
        $query = $company->paymentOptions()
            ->filters($filters)
            ->orderBy('id', $sort);

        if ($paginate) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    public function getPaymentOptionById(Company $company, $id): ?PaymentOption
    {
        return $company->paymentOptions()->find($id);
    }

    public function updatePaymentOption(Company $company, array $data, $id, $type): ?PaymentOption
    {
        $paymentOption = $company->paymentOptions()
            ->where('type', $type)
            ->find($id);

        if (in_array($type, [
            PaymentOptionTypeEnum::UPFRONT_FULL->value,
            PaymentOptionTypeEnum::AFTER_COMPLETION->value,
        ])) {
            throw new \Exception('You can only update partial option.');
        }

        if (! $paymentOption) {
            return null;
        }

        $paymentOption->update($data);

        return $paymentOption;
    }
}
