<?php

namespace App\Repositories\Company;

use App\Enums\EmploymentTypeEnum;
use App\Events\DriverCreatedEvent;
use App\Models\Company;
use App\Models\Driver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class DriverRepository
{
    public function getAllDrivers(
        Company $company,
        $filters = [],
        $sort = 'desc',
        $paginate = true,
        $perPage = 10
    ): Collection|LengthAwarePaginator {
        $query = $company->drivers()
            ->filters($filters)
            ->orderBy('id', $sort);

        if ($paginate) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    public function getDriverById(Company $company, $id): ?Driver
    {
        $driver = $company->drivers()->find($id);
        if (! $driver) {
            return null;
        }

        return $driver;
    }

    public function createDriver(Company $company, array $data): Driver
    {
        $driver = Driver::where('email', $data['email'])->first();

        if (! $driver) {
            $password = generatePassword();

            $driver = Driver::create([
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'dob' => $data['dob'],
                'gender' => $data['gender'],
                'image' => $data['image'] ?? null,
                'license_number' => $data['license_number'],
                'license_expires_at' => $data['license_expires_at'],
                'identity_document' => $data['identity_document'],
                'identity_expires_at' => $data['identity_expires_at'],
                'password' => $password['hashed'],
            ]);

            DriverCreatedEvent::dispatch($driver, $password['plain']);
        }

        if ($this->isAssociatedWithCompany($driver, $company)) {
            throw new \Exception('Driver already exists');
        }

        $pivotData = [
            'full_name' => $data['full_name'] ?? $driver->full_name,
            'phone' => $data['phone'] ?? $driver->phone,
            'image' => $data['image'] ?? $driver->image,
            'is_active' => $data['is_active'] ?? true,
            'hired_at' => Carbon::now(),
            'hourly_rate' => $data['hourly_rate'] ?? Driver::DEFAULT_HOURLY_RATE,
            'duty_hours' => $data['duty_hours'] ?? Driver::DEFAULT_DUTY_HOURS,
            'employment_type' => $data['employment_type'] ?? EmploymentTypeEnum::FULL_TIME->value,
            'hired_at' => $data['hired_at'] ?? Carbon::now(),
            'terminated_at' => $data['terminated_at'] ?? null,
        ];

        $company->drivers()->syncWithoutDetaching([$driver->id => $pivotData]);

        if (! empty($data['emergency_contacts'])) {
            $this->attachEmergencyContacts($driver, $data['emergency_contacts']);
        }

        return $driver;
    }

    public function updateDriver(Company $company, array $data, $id): ?Driver
    {
        $driver = $company->drivers()->find($id);
        if (! $driver) {
            return null;
        }
        $company->drivers()->syncWithoutDetaching([$driver->id => $data]);

        return $driver;
    }

    public function deleteDriver(Company $company, $id): ?bool
    {
        $driver = $company->drivers()->find($id);
        if (! $driver) {
            return null;
        }

        return $this->detachCompany($driver, $company);
    }

    public function terminateDriver(Company $company, int $driverId): bool
    {
        $driver = $company->drivers()->where('drivers.id', $driverId)->first();

        if (! $driver) {
            return false;
        }

        $company->drivers()->updateExistingPivot($driver->id, [
            'is_active' => 0,
            'terminated_at' => Carbon::now(),
        ]);

        return true;
    }

    public function attachCompany(Driver $driver, Company $company): void
    {
        $driver->companies()->attach($company);
    }

    public function detachCompany(Driver $driver, Company $company): void
    {
        $driver->companies()->detach($company);
    }

    private function attachEmergencyContacts(Driver $driver, array $data): void
    {
        $driver->emergencyContacts()->createMany($data);
    }

    private function isAssociatedWithCompany(Driver $driver, Company $company): bool
    {
        return $driver->companies()
            ->wherePivot('company_id', $company->id)
            ->wherePivot('driver_id', $driver->id)
            ->exists();
    }
}
