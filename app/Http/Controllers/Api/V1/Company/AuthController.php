<?php

namespace App\Http\Controllers\Api\V1\Company;

use App\Services\DeviceService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CompanyResource;
use App\Http\Requests\Company\LoginRequest;
use App\Services\CompanyRegistrationService;
use App\Events\CompanySetupSuccessfullyEvent;
use App\Http\Requests\Company\RegisterRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Http\Resources\CompanyDetailResource;

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

    /**
     * @OA\Post(
     *     path="/company/auth/register",
     *     summary="Register a new company and return access token",
     *     description="Registers a company, creates a warehouse, registers device, and issues a JWT access token.",
     *     tags={"Company Authentication"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password","company_name","address","documents"},
     *             @OA\Property(property="email", type="string", format="email", example="company@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123"),
     *             @OA\Property(property="company_name", type="string", example="Acme Inc."),
     *             @OA\Property(property="phone", type="string", example="+1234567890"),
     *             @OA\Property(property="website", type="string", example="https://acme.com"),
     *             @OA\Property(property="logo", type="string", example="https://acme.com/logo.png"),
     *             @OA\Property(property="description", type="string", example="Company description goes here."),
     *             @OA\Property(
     *                 property="address",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"address_line1","city","country"},
     *                     @OA\Property(property="address_line1", type="string", example="123 Business St"),
     *                     @OA\Property(property="address_line2", type="string", example="Suite 100"),
     *                     @OA\Property(property="city", type="string", example="New York"),
     *                     @OA\Property(property="state", type="string", example="NY"),
     *                     @OA\Property(property="country", type="string", example="USA"),
     *                     @OA\Property(property="zip", type="string", example="10001"),
     *                     @OA\Property(property="is_primary", type="boolean", example=true)
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="documents",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"name","type","file_path","mime_type","issued_at","expires_at","is_verified"},
     *                     @OA\Property(property="name", type="string", example="Business License"),
     *                     @OA\Property(property="type", type="string", example="license"),
     *                     @OA\Property(property="file_path", type="string", example="documents/license.pdf"),
     *                     @OA\Property(property="mime_type", type="string", example="application/pdf"),
     *                     @OA\Property(property="issued_at", type="string", format="date", example="2025-01-01"),
     *                     @OA\Property(property="expires_at", type="string", format="date", example="2030-01-01"),
     *                     @OA\Property(property="is_verified", type="boolean", example=true)
     *                 )
     *             ),
     *             @OA\Property(property="device_token", type="string", nullable=true, example="device-uuid-123"),
     *             @OA\Property(property="device_type", type="string", enum={"android","ios"}, example="android"),
     *             @OA\Property(property="device_id", type="string", example="device-uuid-123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Company registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Company registered successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGci..."),
     *                 @OA\Property(property="token_type", type="string", example="bearer"),
     *                 @OA\Property(property="expires_in", type="integer", example=3600),
     *                 @OA\Property(property="data", ref="#/components/schemas/Company")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid credentials")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The email field is required.")
     *         )
     *     )
     * )
     */

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

    /**
     * @OA\Post(
     *     path="/company/auth/login",
     *     summary="Login a company and return access token",
     *     description="Authenticates a company and returns a JWT access token along with company details.",
     *     tags={"Company Authentication"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="company@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Company logged in successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Company logged in successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGci..."),
     *                 @OA\Property(property="token_type", type="string", example="bearer"),
     *                 @OA\Property(property="expires_in", type="integer", example=3600),
     *                 @OA\Property(property="data", ref="#/components/schemas/Company")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid credentials")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The email field is required.")
     *         )
     *     )
     * )
     */

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        if (!$token = Auth::guard('company')->attempt($credentials)) {
            return $this->sendErrorResponse('Invalid credentials');
        }

        $company = Auth::guard('company')->user()->load([
            'generalSettings',
            'addresses',
            'defaultAddress',
            'documents',
            'latestLocation',
            'warehouses',
            'paymentOptions',
            'timings',
            'holidays'
        ]);

        return $this->sendSuccessResponse(
            [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => Auth::guard('company')->factory()->getTTL() * 60,
                'data' => CompanyDetailResource::make($company),
            ],
            'Company logged in successfully',
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *     path="/company/auth/logout",
     *     summary="Logout a company",
     *     description="Logs out a company and invalidates the access token.",
     *     tags={"Company Authentication"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Company logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Company logged out successfully")
     *         )
     *     )
     * )
     */

    public function logout(): JsonResponse
    {
        Auth::guard('company')->logout();
        return $this->sendSuccessResponse(null, 'Company logged out successfully', Response::HTTP_OK);
    }
}
