<?php

namespace App\Repositories\Company;

use Carbon\Carbon;
use App\Models\Company;
use App\Models\Driver;
use App\Models\Holiday;
use App\Enums\HolidayApprovalStatusEnums;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class HolidayRepository
{
    public function getAllHolidays(
        Model $holidayable,
        $filters = [],
        $sort = 'desc',
        $paginate = true,
        $perPage = 10
    ): Collection|LengthAwarePaginator {
        $query = $holidayable->holidays()
            ->filters($filters)
            ->orderBy('id', $sort);

        if ($paginate) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    public function getHolidayById(Model $holidayable, $id): ?Holiday
    {
        return $holidayable->holidays()->find($id);
    }

    public function createHoliday(Model $holidayable, array $data): ?Holiday
    {
        // check if the holiday already exists
        $holiday = $holidayable->holidays()->where('date', $data['date'])->first();
        if ($holiday) {
            throw new \Exception('Holiday already exists');
        }

        if ($holidayable instanceof Company) {
            $data['is_approved'] = HolidayApprovalStatusEnums::APPROVED;
            $data['is_active'] = true;
        }

        if ($holidayable instanceof Driver) {
            $data['is_approved'] = HolidayApprovalStatusEnums::PENDING;
            $data['is_active'] = true;
        }

        return $holidayable->holidays()->create($data);
    }

    public function updateHoliday(Model $holidayable, array $data, $id): ?Holiday
    {
        $holiday = $holidayable->holidays()->find($id);
        if (!$holiday) {
            return null;
        }

        $holiday->update($data);
        return $holiday;
    }

    public function deleteHoliday(Model $holidayable, $id): ?bool
    {
        return $holidayable->holidays()->find($id)->delete();
    }

    function getDaysOfWeekOptions(): array
    {
        return collect(range(0, 6))->map(function ($day) {
            return [
                'value' => $day,
                'label' => Carbon::create()
                    ->startOfWeek(Carbon::SUNDAY) // force start of week to Sunday
                    ->addDays($day)
                    ->format('l'),
            ];
        })->toArray();
    }
}
