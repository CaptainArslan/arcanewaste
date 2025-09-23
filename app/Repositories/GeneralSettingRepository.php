<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class GeneralSettingRepository
{
    public function getAllGeneralSettings(
        Model $generalSettingable,
        $filters = [],
        $sort = 'desc',
        $paginate = true,
        $perPage = 10,
    ) {
        $query = $generalSettingable->generalSettings()
            ->filter($filters)
            ->orderBy('id', $sort);

        if ($paginate) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    public function getGeneralSettingById(Model $generalSettingable, $id)
    {
        return $generalSettingable->generalSettings()->find($id);
    }

    public function createGeneralSetting(Model $generalSettingable, array $data)
    {
        return $generalSettingable->generalSettings()->create($data);
    }

    public function updateGeneralSetting(Model $generalSettingable, array $data, $id, $key)
    {
        $generalSetting = $generalSettingable->generalSettings()
            ->where('key', $key)
            ->find($id);

        if (!$generalSetting) {
            return false;
        }

        $generalSetting->fill($data);
        $generalSetting->save();

        return $generalSetting;
    }



    public function deleteGeneralSetting(Model $generalSettingable, $id)
    {
        return $generalSettingable->generalSettings()->find($id)->delete();
    }

    public function searchGeneralSettings(Model $generalSettingable, $query)
    {
        return $generalSettingable->generalSettings()->where('key', 'like', '%' . $query . '%')->get();
    }
}
