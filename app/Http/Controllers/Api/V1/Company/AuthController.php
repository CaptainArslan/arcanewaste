<?php

namespace App\Http\Controllers\Api\V1\Company;

use App\Models\Company;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\CompanyResource;
use App\Events\CompanySetupSuccessfullyEvent;
use App\Http\Requests\Company\RegisterRequest;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->all();
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
        $address = $company->defaultAddress()->create([
            "address_line1" => $address['address_line1'],
            "address_line2" => $address['address_line2'],
            "city" => $address['city'],
            "state" => $address['state'],
            "postal_code" => $address['postal_code'],
            "country" => $address['country'],
            "latitude" => $address['latitude'],
            "longitude" => $address['longitude'],
            "is_primary" => $address['is_primary'] ?? true,
        ]);

        if ($request->has('documents')) {
            foreach ($request->documents as $document) {
                $company->documents()->create([
                    'name' => $document['name'],
                    'type' => $document['type'],
                    'file_path' => $document['file_path'],
                    'mime_type' => $document['mime_type'],
                    'issued_at' => $document['issued_at'],
                    'expires_at' => $document['expires_at'],
                    'is_verified' => $document['is_verified'],
                ]);
            }
        }

        // create default warehouse
        $warehouse = $company->warehouses()->create([
            'name' => $company->name . " Warehouse",
            'code' => $company->name . " Warehouse",
            'type' => 'storage',
            'capacity' => 1000,
            'is_active' => true,
        ]);

        // create default warehouse address
        $warehouse->defaultAddress()->create($address->toArray());

        $timings = [
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
        ];

        foreach ($timings as $timing) {
            if ($company->timings()->where('day_of_week', $timing)->exists()) {
                continue;
            } else {
                $company->timings()->create([
                    'day_of_week' => $timing,
                    'opens_at' => '09:00',
                    'closes_at' => '17:00',
                    'is_closed' => false,
                ]);
            }

            if ($warehouse->timings()->where('day_of_week', $timing)->exists()) {
                continue;
            } else {
                $warehouse->timings()->create([
                    'day_of_week' => $timing,
                    'opens_at' => '09:00',
                    'closes_at' => '17:00',
                    'is_closed' => false,
                ]);
            }
        }

        // holo
        $holidays = [
            ['name' => 'New Year', 'holiday_date' => now()->year . '-01-01', 'is_recurring' => true],
            ['name' => 'Independence Day', 'holiday_date' => now()->year . '-03-14', 'is_recurring' => true],
        ];
        foreach ($holidays as $holiday) {
            $company->holidays()->create($holiday);
            // $warehouse->holidays()->create($holiday);
        }

        // create default general settings
        $company->generalSettings()->createMany([
            ['key' => 'default_timezone', 'value' => env('DEFAULT_TIMEZONE', 'Asia/Karachi')],
            ['key' => 'order_cancelation_time_limit', 'value' =>  24],
            ['key' => 'default_driver_hourly_rate', 'value' => 10],
            ['key' => 'default_driver_daily_hours', 'value' => 8],
        ]);

        // // create default payment methods
        // $company->paymentMethods()->createMany([
        //     ['name' => 'Finix', 'code' => 'finix', 'status' => 'active', 'requires_merchant_onboarding' => true],
        //     // ['name' => 'Stripe', 'slug' => 'stripe', 'status' => 'active', 'requires_merchant_onboarding' => true],
        //     // ['name' => 'Paypal', 'slug' => 'paypal', 'status' => 'active', 'requires_merchant_onboarding' => true],
        // ]);

        // // create default merchant onboarding logs
        // $company->merchantOnboardingLogs()->createMany([
        //     ['payment_method_code' => 'finix', 'status' => 'pending'],
        //     // ['payment_method_code' => 'stripe', 'status' => 'pending'],
        //     // ['payment_method_code' => 'paypal', 'status' => 'pending'],
        // ]);
        // create default payment options
        $company->paymentOptions()->createMany([
            ['type' => 'upfront_full', 'percentage' => 100],
            ['type' => 'partial_upfront', 'percentage' => 50],
            ['type' => 'after_completion', 'percentage' => 0],
        ]);

        CompanySetupSuccessfullyEvent::dispatch($company);

        $credentials = [
            'email' => $data['email'],
            'password' => $data['password'],
        ];

        if (!$token = Auth::guard('company')->attempt($credentials)) {
            return $this->sendErrorResponse('Invalid credentials');
        }

        if ($data['device_token'] && $data['device_type'] && $data['device_id']) {
            $company->devices()->create([
                'device_token' => $data['device_token'],
                'device_type' => $data['device_type'],
                'device_id' => $data['device_id'],
            ]);
        }

        return $this->sendSuccessResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('company')->factory()->getTTL() * 60,
            'data' => CompanyResource::make($company),
        ], 'Company registered successfully');
    }
}
