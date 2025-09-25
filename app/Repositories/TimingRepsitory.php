<?php

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class TimingRepsitory
{
    public function getAllTimings(Model $timingable, $filters = [], $sort = 'desc', $paginate = true, $perPage = 10): Collection|LengthAwarePaginator
    {
        $query = $timingable->timings()
            ->filters($filters)
            ->orderBy('id', $sort);

        if ($paginate) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    public function getTimingById(Model $timingable, $id): ?Model
    {
        return $timingable->timings()->find($id);
    }

    public function syncCompanyTimings(Model $timingable, array $timings): EloquentCollection
    {
        // Delete old timings
        $timingable->timings()->delete();

        $results = new EloquentCollection();

        foreach ($timings as $timingData) {
            // Calculate hours if not closed
            $opens = strtotime($timingData['opens_at']);
            $closes = strtotime($timingData['closes_at']);
            $hours = ($timingData['is_closed'] ?? false) ? 0 : max(0, ($closes - $opens) / 3600);

            $existing = $timingable->timings()->create([
                'day_of_week' => $timingData['day_of_week'],
                'opens_at' => $timingData['opens_at'],
                'closes_at' => $timingData['closes_at'],
                'is_closed' => $timingData['is_closed'] ?? false,
                'hours' => $hours,
            ]);

            $results->push($existing);
        }

        return $results;
    }
}
