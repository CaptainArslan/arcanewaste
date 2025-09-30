<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class GeneralSettingRepository
{
    public function getAllGeneralSettings(
        Model $generalSettingable,
        $filters = [],
        $sort = 'desc',
        $paginate = true,
        $perPage = 10,
    ): Collection|LengthAwarePaginator {
        $query = $generalSettingable->generalSettings()
            ->filters($filters)
            ->orderBy('id', $sort);

        if ($paginate) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    public function getGeneralSettingById(Model $generalSettingable, $id): ?Model
    {
        return $generalSettingable->generalSettings()->find($id);
    }

    public function updateGeneralSetting(Model $generalSettingable, array $data, $id, $key): ?Model
    {
        $generalSetting = $generalSettingable->generalSettings()
            ->where('key', $key)
            ->find($id);

        if (! $generalSetting) {
            return false;
        }

        $generalSetting->fill($data);
        $generalSetting->save();

        return $generalSetting;
    }
}
