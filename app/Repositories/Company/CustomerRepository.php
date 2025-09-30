<?php

namespace App\Repositories\Company;

use App\Events\CustomerCreatedEvent;
use App\Models\Company;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerRepository
{
    public function getAllCustomers(
        Company $company,
        $filters = [],
        $sort = 'desc',
        $paginate = true,
        $perPage = 10
    ): Collection|LengthAwarePaginator {
        $query = $company->customers()
            ->withPivot([
                'full_name',
                'phone',
                'image',
                'is_active',
                'is_delinquent',
                'delinquent_days',
                'created_at',
                'updated_at',
            ])
            ->filters($filters)
            ->orderBy('id', $sort);

        if ($paginate) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    public function getCustomerById(Company $company, $id): ?Customer
    {
        return $company->customers()->find($id);
    }

    public function createCustomer(Company $company, array $data): Customer
    {
        $password = generatePassword();
        $customer = Customer::where('email', $data['email'])->first();

        if (! $customer) {
            $customer = Customer::Create([
                'email' => $data['email'],
                'full_name' => $data['full_name'] ?? 'Unknown',
                'phone' => $data['phone'] ?? null,
                'image' => $data['image'] ?? null,
                'password' => $password['hashed'],
                'system_generated_password' => true,
            ]);
        }

        if ($this->isAssociatedWithCompany($customer, $company)) {
            throw new \Exception('Customer already exists');
        }

        // Attach or update pivot data
        $pivotData = [
            'full_name' => $data['full_name'] ?? $customer->full_name,
            'phone' => $data['phone'] ?? $customer->phone,
            'image' => $data['image'] ?? $customer->image,
            'is_active' => $data['is_active'] ?? true,
            'is_delinquent' => $data['is_delinquent'] ?? false,
            'delinquent_days' => $data['delinquent_days'] ?? 0,
        ];

        // Sync without detaching (avoids duplicate attach)
        $company->customers()->syncWithoutDetaching([$customer->id => $pivotData]);

        // Only dispatch event if customer was just created
        if ($customer->wasRecentlyCreated) {
            CustomerCreatedEvent::dispatch($customer, $password['plain']);
        }

        // Attach emergency contacts if provided
        if (! empty($data['emergency_contacts'])) {
            $this->attachEmergencyContacts($customer, $data['emergency_contacts']);
        }

        return $customer;
    }

    public function updateCustomer(Company $company, array $data, Customer $customer): ?Customer
    {
        if (! $this->isAssociatedWithCompany($customer, $company)) {
            return null;
        }

        if (isset($data['email']) && $data['email'] !== $customer->email) {
            $exists = Customer::where('email', $data['email'])
                ->where('id', '!=', $customer->id)
                ->exists();
            if ($exists) {
                throw new \Exception('Email already taken by another customer.');
            }
        }

        $customer->update($data);

        if (isset($data['address'])) {
            $customer->updateAddress($data['address'], $data['address']['is_primary'] ?? false);
        }

        if (isset($data['emergency_contacts'])) {
            $customer->updateEmergencyContacts($data['emergency_contacts']);
        }

        return $customer;
    }

    public function deleteCustomer(Company $company, $id): ?bool
    {
        $customer = $company->customers()->find($id);
        if (! $customer) {
            return null;
        }

        $customer->companies()->detach($company);

        return true;
    }

    private function isAssociatedWithCompany(Customer $customer, Company $company): bool
    {
        return $company->customers()
            ->wherePivot('company_id', $company->id)
            ->wherePivot('customer_id', $customer->id)
            ->exists();
    }

    private function detachCustomerFromCompany(Customer $customer, Company $company): void
    {
        $customer->companies()->detach($company);
    }

    private function attachEmergencyContacts(Customer $customer, array $data): void
    {
        $customer->emergencyContacts()->delete();
        $customer->emergencyContacts()->createMany($data);
    }

    private function detachEmergencyContacts(Customer $customer): void
    {
        $customer->emergencyContacts()->delete();
    }
}
