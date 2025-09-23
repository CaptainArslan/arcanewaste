<?php

namespace App\Repositories;

use App\Models\Company;
use App\repositories\Contracts\CompanyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class EloquentCompanyRepository implements CompanyRepositoryInterface
{
    public function getAllCompanies(): Collection
    {
        return Company::all();
    }

    public function getCompanyById($id): ?Company
    {
        return Company::find($id);
    }

    public function createCompany(array $data): Company
    {
        // Use transaction to avoid partial creation
        return DB::transaction(function () use ($data) {

            // Step 1: Create company
            $company = Company::create($data);

            // Step 2: Default company address
            $company->addresses()->create([
                'address_line_1' => 'Company Address',
                'address_line_2' => 'Company Address 2',
                'label' => 'Company Address',
                'city' => 'Company City',
                'state' => 'Company State',
                'country' => 'PK',
                'postal_code' => '00000',
                'is_primary' => true,
            ]);

            // Step 3: Default warehouse + address + timings
            $warehouse = $company->warehouses()->create([
                'name' => 'Main Warehouse',
                'code' => 'MAIN_WAREHOUSE',
                'type' => 'storage',
                'capacity' => 1000,
                'is_active' => true,
            ]);

            $warehouse->addresses()->create([
                'address_line_1' => 'Warehouse Address',
                'address_line_2' => 'Warehouse Address 2',
                'label' => 'Warehouse Address',
                'city' => 'Warehouse City',
                'state' => 'Warehouse State',
                'country' => 'PK',
                'postal_code' => '00000',
                'is_primary' => true,
            ]);

            // Add weekly timings
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'holiday', 'other'];
            foreach ($days as $day) {
                $warehouse->timings()->create([
                    'day_of_week' => $day,
                    'opens_at' => '09:00',
                    'closes_at' => '17:00',
                    'is_closed' => $day === 'holiday' ? true : false,
                ]);
            }

            // Step 4: General settings
            $company->generalSettings()->createMany([
                ['key' => 'default_timezone', 'value' => 'Asia/Karachi'],
                ['key' => 'order_cancelation_time_limit', 'value' => 24], // hours
                ['key' => 'default_driver_hourly_rate', 'value' => 10],
            ]);

            // Step 5: Payment methods
            $defaultPaymentMethods = ['finix', 'stripe', 'paypal'];
            foreach ($defaultPaymentMethods as $methodCode) {
                $company->paymentMethods()->create([
                    'name' => ucfirst($methodCode),
                    'slug' => $methodCode,
                    'status' => 'active',
                    'requires_merchant_onboarding' => true,
                ]);
            }

            // Step 6: Merchant onboarding logs (empty initially)
            foreach ($defaultPaymentMethods as $methodCode) {
                $company->merchantOnboardingLogs()->create([
                    'payment_method_code' => $methodCode,
                    'status' => 'pending',
                ]);
            }

            // Step 7: Dumpster sizes
            $company->dumpsterSizes()->create([
                'name' => '10 Yard Dumpster',
                'code' => '10_YARD_DUMPSTER',
                'base_rate' => 10000,
                'extra_day_rate' => 100,
                'overdue_rate' => 200,
                'min_rental_days' => 1,
                'max_rental_days' => null,
                'volume_cubic_yards' => 10,
                'weight_limit_lbs' => 4000,
                'is_active' => true,
            ]);

            // Step 8: Payment options
            $defaultPaymentOptions = [
                ['name' => 'Full Upfront', 'code' => 'UPFRONT_FULL'],
                ['name' => 'Partial Upfront', 'code' => 'PARTIAL_UPFRONT'],
                ['name' => 'After Completion', 'code' => 'AFTER_COMPLETION'],
            ];
            $company->paymentOptions()->createMany($defaultPaymentOptions);

            // Step 9: Waste types
            $company->wasteTypes()->create([
                'name' => 'Construction Debris',
                'code' => 'CONSTRUCTION_DEBRIS',
                'is_hazardous' => false,
                'extra_fee' => 0,
                'is_active' => true,
            ]);

            // Step 10: Taxes
            $company->taxes()->create([
                'name' => 'Sales Tax',
                'code' => 'SALES_TAX',
                'rate' => 15,
                'is_active' => true,
            ]);

            // Step 11: Promotions
            $company->promotions()->create([
                'name' => 'Spring Sale',
                'code' => 'SPRING_SALE',
                'discount_type' => 'percentage',
                'discount_amount' => 10,
                'is_active' => true,
            ]);

            // Step 12: Documents (empty, but you can create placeholders if needed)
            $company->documents()->create([
                'name' => 'Company Document Placeholder',
                'type' => 'document',
                'path' => null,
            ]);

            // Step 13: Holidays
            $company->holidays()->create([
                'name' => 'Independence Day',
                'holiday_date' => '2025-08-14',
                'is_recurring' => true,
            ]);

            // Step 14: Company driver
            $company->drivers()->create([
                'name' => 'Company Driver',
                'email' => 'company@driver.com',
                'phone' => '03001234567',
            ]);

            // Step 15: Company customer
            $company->customers()->create([
                'name' => 'Company Customer',
                'email' => 'company@customer.com',
                'phone' => '03001234567',
            ]);

            $defaultHolidays = [
                ['name' => 'New Year\'s Day', 'holiday_date' => now()->year.'-01-01', 'is_recurring' => true],
                ['name' => 'Independence Day', 'holiday_date' => now()->year.'-08-14', 'is_recurring' => true],
                ['name' => 'Christmas', 'holiday_date' => now()->year.'-12-25', 'is_recurring' => true],
            ];

            foreach ($defaultHolidays as $holiday) {
                $company->holidays()->create($holiday);
            }

            return $company;
        });
    }

    public function updateCompany(array $data, $id): ?Company
    {
        return Company::find($id)->update($data);
    }

    public function deleteCompany($id): ?Company
    {
        return Company::find($id)->delete();
    }

    public function searchCompanies($query): Company|null|Collection
    {
        return Company::where('name', 'like', '%'.$query.'%')->get();
    }
}
