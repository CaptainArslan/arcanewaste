<?php

namespace App\Http\Controllers\Api\V1\Company;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CompanyResource;
use App\Services\CompanyRegistrationService;
use App\Events\CompanySetupSuccessfullyEvent;
use App\Http\Requests\Company\RegisterRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Services\DeviceService;

class AuthController extends Controller
{
    private $companyRegistrationService;
    private $deviceService;
    public function __construct(
        CompanyRegistrationService $companyRegistrationService,
        DeviceService $deviceService
    ) {
        $this->companyRegistrationService = $companyRegistrationService;
        $this->deviceService = $deviceService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $company = $this->companyRegistrationService->registerCompany($request->all());
        $this->companyRegistrationService->registerWarehouse($company);

        CompanySetupSuccessfullyEvent::dispatch($company);

        $data = $request->all();
        $credentials = [
            'email' => $data['email'],
            'password' => $data['password'],
        ];

        if (!$token = Auth::guard('company')->attempt($credentials)) {
            return $this->sendErrorResponse('Invalid credentials');
        }

        $this->deviceService->registerDevice($company, $data);

        return $this->sendSuccessResponse(
            [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => Auth::guard('company')->factory()->getTTL() * 60,
                'data' => CompanyResource::make($company),
            ],
            'Company registered successfully',
            Response::HTTP_CREATED
        );
    }
}
