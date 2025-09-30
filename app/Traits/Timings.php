<?php

namespace App\Traits;

use App\Models\Timing;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Timings
{
    public function timings(): MorphMany
    {
        return $this->morphMany(Timing::class, 'timeable');
    }

    public function syncTimings(array $timings): void
    {
        $this->timings()->createMany($timings);
    }

    public function getTimings(): Collection
    {
        return $this->timings()->get();
    }
}
