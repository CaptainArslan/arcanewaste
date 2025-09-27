<?php

namespace App\Repositories\Company;

use App\Models\Company;
use App\Models\Customer;
use Illuminate\Support\Str;
use App\Events\CustomerCreatedEvent;
use Illuminate\Support\Facades\Hash;
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
        $password = $this->generatePassword();

        // Create or fetch existing customer globally
        $customer = Customer::firstOrCreate(
            ['email' => $data['email']],
            [
                'full_name' => $data['full_name'] ?? 'Unknown',
                'phone' => $data['phone'] ?? null,
                'image' => $data['image'] ?? null,
                'password' => $password['hashed'],
                'system_generated_password' => true,
            ]
        );


        // Only dispatch event if customer was newly created
        if ($customer->wasRecentlyCreated || ! $company->customers()->where('customers.id', $customer->id)->exists()) {
            $company->customers()->attach($customer->id, [
                'full_name' => $data['full_name'] ?? $customer->full_name,
                'phone' => $data['phone'] ?? $customer->phone,
                'image' => $data['image'] ?? $customer->image,
                'is_active' => $data['is_active'] ?? true,
                'is_delinquent' => $data['is_delinquent'] ?? false,
                'delinquent_days' => $data['delinquent_days'] ?? 0,
            ]);

            CustomerCreatedEvent::dispatch($customer, $password['plain']);
        }

        // Attach emergency contacts if provided
        if (isset($data['emergency_contacts'])) {
            $this->attachEmergencyContacts($customer, $data['emergency_contacts']);
        }

        return $customer;
    }


    public function generatePassword(): array
    {
        // Use a fixed password for local/testing environment
        $plainPassword = app()->environment('local', 'testing') ? 'password' : Str::random(10);

        // Hash the password
        $hashedPassword = Hash::make($plainPassword);

        // Return both plain and hashed password
        return [
            'plain' => $plainPassword,
            'hashed' => $hashedPassword,
        ];
    }


    public function updateCustomer(Company $company, array $data, $id): ?Customer
    {
        $customer = $company->customers()->find($id);
        if (!$customer) {
            return null;
        }
        $customer->update($data);
        if (isset($data['address'])) {
            $this->updateAddress($customer, $data['address']);
        }
        if (isset($data['emergency_contacts'])) {
            $this->updateEmergencyContacts($customer, $data['emergency_contacts']);
        }
        return $customer;
    }

    public function deleteCustomer(Company $company, $id): ?bool
    {
        $customer = $company->customers()->find($id);
        if (!$customer) {
            return null;
        }
        $this->detachCustomerFromCompany($customer, $company);
        $customer->delete();
        return true;
    }

    // check for the customer if it is attached to the company
    private function isCustomerAttachedToCompany(Customer $customer, Company $company): bool
    {
        return $company->customers()
            ->wherePivot('company_id', $company->id)
            ->wherePivot('customer_id', $customer->id)
            ->exists();
    }

    private function attachCustomerToCompany(Customer $customer, Company $company): void
    {
        if ($this->isCustomerAttachedToCompany($customer, $company)) {
            throw new \Exception('Customer is already attached to the company');
        }
        $customer->companies()->attach([
            'company_id' => $company->id,
            'customer_id' => $customer->id,
            'full_name' => $customer->full_name,
            'phone' => $customer->phone,
            'image' => $customer->image,
            'is_active' => $customer->is_active,
            'is_delinquent' => $customer->is_delinquent,
            'delinquent_days' => $customer->delinquent_days,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function detachCustomerFromCompany(Customer $customer, Company $company): void
    {
        $customer->companies()->detach($company);
    }

    private function attachAddress(Customer $customer, array $data): void
    {
        $customer->createAddress($customer, $data, true);
    }

    private function updateAddress(Customer $customer, array $data): void
    {
        $customer->updateAddress($customer, $data, true);
    }

    private function detachAddress(Customer $customer): void
    {
        $customer->addresses()->delete();
    }

    private function attachEmergencyContacts(Customer $customer, array $data): void
    {
        $customer->emergencyContacts()->createMany($data);
    }

    private function updateEmergencyContacts(Customer $customer, array $data): void
    {
        $customer->emergencyContacts()->updateMany($data);
    }

    private function detachEmergencyContacts(Customer $customer): void
    {
        $customer->emergencyContacts()->delete();
    }
}
