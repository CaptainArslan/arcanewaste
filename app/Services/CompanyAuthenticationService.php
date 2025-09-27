<?php

namespace App\Services;

use Carbon\Carbon;
use App\Mail\OtpMail;
use App\Models\Company;
use App\Models\Warehouse;
use App\Models\PasswordResetTokens;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Enums\TaxEnums;
use App\Enums\HolidayApprovalStatusEnum;
use Illuminate\Database\Eloquent\Collection;

class CompanyAuthenticationService
{
    public function registerCompany(array $data): Company
    {
        $address = $data['address'];
        $company = Company::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'logo' => $data['logo'],
            'description' => $data['description'],
            'phone' => $data['phone'],
            'website' => $data['website'],
        ]);

        // create default address
        $address = $company->createAddress($company, $address, true);
        $company->createDocuments($company, $data['documents'] ?? []);
        $this->createCompanyTimings($company);
        $this->createCompanyHolidays($company);
        $this->createCompanyGeneralSettings($company);
        $this->createCompanyPaymentOptions($company);
        $this->createTaxes($company);
        $this->createDumpsterSizes($company);
        return $company;
    }

    public function registerWarehouse(Company $company): Warehouse
    {
        $warehouse = $company->warehouses()->create([
            'name' => $company->name . ' Warehouse',
            'code' => $company->name . '-' . $company->id . ' Warehouse',
            'type' => 'storage',
            'capacity' => 1000,
            'is_active' => true,
        ]);

        $warehouse->createAddress($warehouse, $company->defaultAddress->toArray() ?? [], true);
        // $this->createWarehouseTimings($warehouse);

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
            if (! in_array($timing, $existingDays)) {
                $newTimings[] = [
                    'timeable_id' => $company->id,
                    'timeable_type' => Company::class,
                    'day_of_week' => $timing,
                    'opens_at' => '09:00',
                    'closes_at' => '17:00',
                    'is_closed' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
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
            if (! in_array($timing, $existingDays)) {
                $newTimings[] = [
                    'timeable_id' => $warehouse->id,
                    'timeable_type' => Warehouse::class,
                    'day_of_week' => $timing,
                    'opens_at' => '09:00',
                    'closes_at' => '17:00',
                    'is_closed' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        return $warehouse->timings()->createMany($newTimings);
    }

    // private function createAddress(Model $addressable, array $address = [], bool $isPrimary = false): Address
    // {
    //     if (empty($address)) {
    //         return $addressable->addresses()->where('is_primary', true)->first();
    //     }

    //     // If another primary exists and we are not overriding, force false
    //     if (! $isPrimary && $addressable->addresses()->where('is_primary', true)->exists()) {
    //         $isPrimary = false;
    //     }

    //     return $addressable->addresses()->create([
    //         'address_line1' => $address['address_line1'] ?? null,
    //         'address_line2' => $address['address_line2'] ?? null,
    //         'city' => $address['city'] ?? null,
    //         'state' => $address['state'] ?? null,
    //         'postal_code' => $address['postal_code'] ?? null,
    //         'country' => $address['country'] ?? null,
    //         'latitude' => $address['latitude'] ?? null,
    //         'longitude' => $address['longitude'] ?? null,
    //         'is_primary' => $isPrimary,
    //     ]);
    // }

    // private function createDocuments(Model $documentable, array $documents = []): Collection
    // {
    //     if (empty($documents)) {
    //         return $documentable->documents()->get();
    //     }

    //     $newDocuments = [];
    //     foreach ($documents as $document) {
    //         $existingDocuments = $documentable->documents()->pluck('file_path')->toArray();
    //         if (! in_array($document['file_path'], $existingDocuments)) {
    //             $newDocuments[] = [
    //                 'name' => $document['name'],
    //                 'type' => $document['type'],
    //                 'file_path' => $document['file_path'],
    //                 'mime_type' => $document['mime_type'],
    //                 'issued_at' => $document['issued_at'],
    //                 'expires_at' => $document['expires_at'],
    //                 'is_verified' => $document['is_verified'],
    //             ];
    //         }
    //     }

    //     return $documentable->documents()->createMany($newDocuments);
    // }

    private function createCompanyHolidays(Company $company): Collection
    {
        $holidays = [
            [
                'name'            => 'New Year',
                'date'            => now()->year . '-01-01',
                'recurrence_type' => 'yearly',
                'month_day'       => '01-01',
                'reason'          => 'Public holiday',
                'is_approved'     => HolidayApprovalStatusEnum::APPROVED,  // company holidays auto-approved
                'is_active'       => true,
            ],
            [
                'name'            => 'Pakistan Day',
                'date'            => now()->year . '-03-23',
                'recurrence_type' => 'yearly',
                'month_day'       => '03-23',
                'reason'          => 'National holiday',
                'is_approved'     => HolidayApprovalStatusEnum::APPROVED,
                'is_active'       => true,
            ],
            [
                'name'            => 'Independence Day',
                'date'            => now()->year . '-08-14',
                'recurrence_type' => 'yearly',
                'month_day'       => '08-14',
                'reason'          => 'National holiday',
                'is_approved'     => HolidayApprovalStatusEnum::APPROVED,
                'is_active'       => true,
            ],
            [
                'name'            => 'Friday Weekly Holiday',
                'recurrence_type' => 'weekly',
                'day_of_week'     => 5, // 0=Sunday ... 6=Saturday
                'reason'          => 'Weekly company closure',
                'is_approved'     => HolidayApprovalStatusEnum::APPROVED,
                'is_active'       => true,
            ],
        ];

        return $company->holidays()->createMany($holidays);
    }


    private function createCompanyGeneralSettings(Company $company): Collection
    {
        return $company->generalSettings()->createMany([
            [
                'key' => 'default_timezone',
                'value' => env('DEFAULT_TIMEZONE', 'Asia/Karachi'),
                'type' => 'string',
                'description' => 'The default timezone for the company'
            ],
            [
                'key' => 'order_cancelation_time_limit',
                'value' => 24,
                'type' => 'integer',
                'description' => 'The time limit for canceling an order in hours'
            ],
            [
                'key' => 'default_driver_hourly_rate',
                'value' => 10,
                'type' => 'float',
                'description' => 'The default hourly rate for a driver in USD'
            ],
        ]);
    }

    private function createCompanyPaymentOptions(Company $company): Collection
    {
        return $company->paymentOptions()->createMany([
            [
                'name' => 'Full Upfront',
                'type' => 'upfront_full',
                'percentage' => 100,
                'description' => 'The percentage of the order amount that is paid upfront'
            ],
            [
                'name' => 'Partial Upfront',
                'type' => 'partial_upfront',
                'percentage' => 50,
                'description' => 'The percentage of the order amount that is paid upfront'
            ],
            [
                'name' => 'After Completion',
                'type' => 'after_completion',
                'percentage' => 0,
                'description' => 'The percentage of the order amount that is paid after the completion of the order'
            ],
        ]);
    }

    private function createTaxes(Company $company): Collection
    {
        return $company->taxes()->createMany([
            [
                'name' => 'Sales Tax',
                'type' => TaxEnums::PERCENTAGE,
                'rate' => 10,
                'is_active' => true,
            ]
        ]);
    }

    private function createDumpsterSizes(Company $company): Collection
    {
        return $company->dumpsterSizes()->createMany([
            [
                'name' => '10 Yard Dumpster',
                'code' => '10YD',
                'description' => '10 cubic yard dumpster',
                'min_rental_days' => 1,
                'max_rental_days' => 14,
                'base_rent' => 100,
                'extra_day_rent' => 10,
                'overdue_rent' => 20,
                'volume_cubic_yards' => 10,
                'weight_limit_lbs' => 2000,
                'is_active' => true,
                'image' => 'https://developers.elementor.com/docs/assets/img/elementor-placeholder-image.png',
                'taxes' => [
                    $company->taxes()->where('name', 'Sales Tax')->first()->id,
                ],
            ]
        ]);
    }

    public function sendOtp(string $email): array
    {
        $minutes = 5;
        $otp = rand(100000, 999999);

        $passwordResetToken = PasswordResetTokens::updateOrCreate([
            'email' => $email,
        ], [
            'token' => $otp,
            'expires_at' => Carbon::now()->addMinutes($minutes),
        ]);

        $name = $this->getNameFromEmail($email);

        $emailSent = $this->sendEmail($email, $name, $otp, $minutes);

        return [
            'success' => $emailSent,
            'message' => $emailSent
                ? 'OTP generated and sent successfully'
                : 'OTP generated but failed to send email',
            'data' => [
                'email' => $email,
                'name' => $name,
                'otp_expired_at' => $passwordResetToken->expires_at->toDateTimeString(),
            ],
        ];
    }

    public function sendEmail(string $email, string $name, string $otp, int $minutes): bool
    {
        try {
            Mail::to($email)->send(
                new OtpMail($otp, $name, $minutes)
            );

            return true;
        } catch (\Throwable $th) {
            Log::error('Failed to send OTP email: ' . $th->getMessage());

            return false;
        }
    }

    public function getNameFromEmail(string $email): string
    {
        return explode('@', $email)[0];
    }

    public function deletePasswordResetToken(string $token): void
    {
        PasswordResetTokens::where('token', $token)->delete();
    }

    public function verifyPasswordResetToken(string $email, string $otp): bool
    {
        $passwordResetToken = PasswordResetTokens::where('email', $email)->where('token', $otp)->first();

        return $passwordResetToken && $passwordResetToken->expires_at > now();
    }

    public function resetPassword(string $email, string $password, string $otp): Company
    {
        if (! $this->verifyPasswordResetToken($email, $otp)) {
            throw new \Exception('Invalid password reset token');
        }

        $company = Company::where('email', $email)->first();
        $company->password = Hash::make($password);
        $company->save();

        $this->deletePasswordResetToken($otp);

        return $company;
    }

    public function updatePassword(string $oldPassword, string $newPassword): Company
    {
        $company = Auth::guard('company')->user();

        // check for the current password
        if (! Hash::check($oldPassword, $company->password)) {
            throw new \Exception('Invalid current password');
        }

        $company->update([
            'password' => Hash::make($newPassword),
        ]);

        return $company;
    }
}
