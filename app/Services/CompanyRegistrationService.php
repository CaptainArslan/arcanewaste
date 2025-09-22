<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Company;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class CompanyRegistrationService
{
    public function registerCompany(array $data ): Company
    {
        $address = $data['address'];
        $company = Company::create([
            "name" => $data['name'],
            "email" => $data['email'],
            "password" => Hash::make($data['password']),
            "logo" => $data['logo'],
            "description" => $data['description'],
            "phone" => $data['phone'],
            "website" => $data['website']
        ]);

        // create default address
        $address = $this->createAddress($company, $address, true);
        $this->createDocuments($company, $data['documents'] ?? []);
        $this->createCompanyTimings($company);
        $this->createCompanyHolidays($company);
        $this->createCompanyGeneralSettings($company);
        $this->createCompanyPaymentOptions($company);
        return $company;
    }

    public function registerWarehouse(Company $company): Warehouse
    {
        $warehouse = $company->warehouses()->create([
            'name' => $company->name . " Warehouse",
            'code' => $company->name . " Warehouse",
            'type' => 'storage',
            'capacity' => 1000,
            'is_active' => true,
        ]);

        $this->createAddress($warehouse, $company->defaultAddress->toArray() ?? [], true);
        $this->createWarehouseTimings($warehouse);
        return $warehouse;
    }

    public function getTimings(): array
    {
        return [
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
        ];
    }

    private function createCompanyTimings(Company $company): Collection
    {
        $timings = $this->getTimings();
        $now = now();

        $existingDays = $company->timings()->pluck('day_of_week')->toArray();
        $newTimings = [];

        foreach ($timings as $timing) {
            if (!in_array($timing, $existingDays)) {
                $newTimings[] = [
                    'timeable_id'  => $company->id,
                    'timeable_type'  => Company::class,
                    'day_of_week' => $timing,
                    'opens_at'    => '09:00',
                    'closes_at'   => '17:00',
                    'is_closed'   => false,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }
        }

        return $company->timings()->createMany($newTimings);
    }

    private function createWarehouseTimings(Warehouse $warehouse): Collection
    {
        $timings = $this->getTimings();
        $now = now();
        $newTimings = [];
        $existingDays = $warehouse->timings()->pluck('day_of_week')->toArray();
        foreach ($timings as $timing) {
            if (!in_array($timing, $existingDays)) {
                $newTimings[] = [
                    'timeable_id'  => $warehouse->id,
                    'timeable_type'  => Warehouse::class,
                    'day_of_week' => $timing,
                    'opens_at'    => '09:00',
                    'closes_at'   => '17:00',
                    'is_closed'   => false,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }
        }
        return $warehouse->timings()->createMany($newTimings);
    }

    private function createAddress(Model $addressable, array $address = [], bool $isPrimary = false): Address
    {
        if (empty($address)) {
            return $addressable->addresses()->where('is_primary', true)->first();
        }

        // If another primary exists and we are not overriding, force false
        if (!$isPrimary && $addressable->addresses()->where('is_primary', true)->exists()) {
            $isPrimary = false;
        }

        return $addressable->addresses()->create([
            'address_line1' => $address['address_line1'] ?? null,
            'address_line2' => $address['address_line2'] ?? null,
            'city'          => $address['city'] ?? null,
            'state'         => $address['state'] ?? null,
            'postal_code'   => $address['postal_code'] ?? null,
            'country'       => $address['country'] ?? null,
            'latitude'      => $address['latitude'] ?? null,
            'longitude'     => $address['longitude'] ?? null,
            'is_primary'    => $isPrimary,
        ]);
    }

    private function createDocuments(Model $documentable, array $documents = []): Collection
    {
        if (empty($documents)) {
            return $documentable->documents()->get();
        }

        $newDocuments = [];
        foreach ($documents as $document) {
            $existingDocuments = $documentable->documents()->pluck('file_path')->toArray();
            if (!in_array($document['file_path'], $existingDocuments)) {
                $newDocuments[] = [
                    'name' => $document['name'],
                    'type' => $document['type'],
                    'file_path' => $document['file_path'],
                    'mime_type' => $document['mime_type'],
                    'issued_at' => $document['issued_at'],
                    'expires_at' => $document['expires_at'],
                    'is_verified' => $document['is_verified'],
                ];
            }
        }
        return $documentable->documents()->createMany($newDocuments);
    }

    private function createCompanyHolidays(Company $company): Collection
    {
        $holidays = [
            ['name' => 'New Year', 'holiday_date' => now()->year . '-01-01', 'is_recurring' => true],
            ['name' => 'Independence Day', 'holiday_date' => now()->year . '-03-14', 'is_recurring' => true],
        ];
        return $company->holidays()->createMany($holidays);
    }

    private function createCompanyGeneralSettings(Company $company): Collection
    {
        return $company->generalSettings()->createMany([
            ['key' => 'default_timezone', 'value' => env('DEFAULT_TIMEZONE', 'Asia/Karachi')],
            ['key' => 'order_cancelation_time_limit', 'value' =>  24],
            ['key' => 'default_driver_hourly_rate', 'value' => 10],
        ]);
    }

    private function createCompanyPaymentOptions(Company $company): Collection
    {
        return $company->paymentOptions()->createMany([
            ['name' => 'Full Upfront', 'type' => 'upfront_full', 'percentage' => 100],
            ['name' => 'Partial Upfront', 'type' => 'partial_upfront', 'percentage' => 50],
            ['name' => 'After Completion', 'type' => 'after_completion', 'percentage' => 0],
        ]);
    }
}
