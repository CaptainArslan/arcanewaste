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
            ->filters($filters)
            ->orderBy('id', $sort);

        if ($paginate) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    public function getCustomerById(Company $company, $id): ?Customer
    {
        $customer = $company->customers()
            ->where('customers.id', $id)
            ->first();

        if (! $customer) {
            throw new \Exception('Customer not found');
        }

        return $customer;
    }

    public function createCustomer(Company $company, array $data): Customer
    {
        $password = generatePassword();

        $customer = Customer::where('email', $data['email'])->first();

        if (! $customer) {
            $customer = Customer::create([
                'email' => $data['email'],
                'full_name' => $data['full_name'] ?? 'Unknown',
                'phone' => $data['phone'] ?? null,
                'image' => $data['image'] ?? null,
                'password' => $password['hashed'],
                'system_generated_password' => true,
                'gender' => $data['gender'] ?? null,
                'dob' => $data['dob'] ?? null,
            ]);
        }

        if ($this->isAssociatedWithCompany($customer, $company)) {
            throw new \Exception('Customer already exists');
        }

        $pivotData = [
            'full_name' => $data['full_name'] ?? $customer->full_name,
            'phone' => $data['phone'] ?? $customer->phone,
            'image' => $data['image'] ?? $customer->image,
            'is_active' => $data['is_active'] ?? true,
            'is_delinquent' => $data['is_delinquent'] ?? false,
            'delinquent_days' => $data['delinquent_days'] ?? 0,
        ];

        $company->customers()->syncWithoutDetaching([$customer->id => $pivotData]);

        // Safe address creation
        if (!empty($data['address'])) {
            $customer->createAddress($data['address'], $data['address']['is_primary'] ?? false);
        }

        // Safe emergency contacts attachment
        if (!empty($data['emergency_contacts'])) {
            $this->attachEmergencyContacts($customer, $data['emergency_contacts']);
        }

        if ($customer->wasRecentlyCreated) {
            CustomerCreatedEvent::dispatch($customer, $password['plain']);
        }

        return $customer;
    }

    public function updateCustomer(Company $company, array $data, Customer $customer): ?Customer
    {
        if (! $this->isAssociatedWithCompany($customer, $company)) {
            throw new \Exception('Customer is not associated with this company');
        }

        // Update pivot
        $company->customers()->syncWithoutDetaching([$customer->id => $data]);

        // Reload customer with pivot for this company
        return $company->customers()
            ->where('customers.id', $customer->id)
            ->first();
    }

    public function deleteCustomer(Company $company, Customer $customer): bool
    {
        if (! $this->isAssociatedWithCompany($customer, $company)) {
            throw new \Exception('Customer is not associated with this company');
        }

        // Only detach the customer from this company
        return $this->detachCustomerFromCompany($customer, $company);
    }

    private function detachCustomerFromCompany(Customer $customer, Company $company): bool
    {
        // detach returns number of rows affected
        $detached = $customer->companies()->detach($company->id);
        return $detached > 0;
    }

    private function isAssociatedWithCompany(Customer $customer, Company $company): bool
    {
        return $company->customers()
            ->wherePivot('company_id', $company->id)
            ->wherePivot('customer_id', $customer->id)
            ->exists();
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
