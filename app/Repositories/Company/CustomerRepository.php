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
            throw new \Exception('Customer is not associated with this company');
        }

        $pivotData = [
            'full_name'       => $data['full_name'] ?? null,
            'phone'           => $data['phone'] ?? null,
            'image'           => $data['image'] ?? null,
            'is_active'       => $data['is_active'] ?? 1,
            'is_delinquent'   => $data['is_delinquent'] ?? 0,
            'delinquent_days' => $data['delinquent_days'] ?? 0,
        ];

        $company->customers()->syncWithoutDetaching([$customer->id => $pivotData]);

        return $customer->fresh(['companies']);
    }


    public function deleteCustomer(Company $company, Customer $customer): ?bool
    {
        if (! $this->isAssociatedWithCompany($customer, $company)) {
            throw new \Exception('Customer is not associated with this company');
        }
        
        return $this->detachCustomerFromCompany($customer, $company);
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
