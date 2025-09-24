<?php

namespace App\Http\Controllers\Api\V1\Company;

use App\Events\CompanySetupSuccessfullyEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Company\ForgotPassword;
use App\Http\Requests\Company\GetOtpRequest;
use App\Http\Requests\Company\LoginRequest;
use App\Http\Requests\Company\RegisterRequest;
use App\Http\Requests\Company\ResetPasswordRequest;
use App\Http\Requests\Company\UpdatePassword;
use App\Http\Resources\CompanyDetailResource;
use App\Models\PasswordResetTokens;
use App\Services\companyAuthenticationService;
use App\Services\DeviceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    private $companyAuthenticationService;

    private $deviceService;

    public function __construct(
        CompanyAuthenticationService $companyAuthenticationService,
        DeviceService $deviceService
    ) {
        $this->companyAuthenticationService = $companyAuthenticationService;
        $this->deviceService = $deviceService;
    }

    /**
     * @OA\Post(
     *     path="/company/auth/send-otp",
     *     summary="Send OTP to company email",
     *     description="Generates a one-time password (OTP) and sends it to the company's registered email address.",
     *     tags={"Company Authentication"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             type="object",
     *             required={"email"},
     *
     *             @OA\Property(property="email", type="string", format="email", example="company@example.com")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="OTP sent successfully"),
     *             @OA\Property(property="data", type="string", nullable=true, example=null)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The email field is required."),
     *             @OA\Property(property="data", type="string", nullable=true, example=null)
     *         )
     *     )
     * )
     */
    public function sendOtp(GetOtpRequest $request): JsonResponse
    {
        $email = $request->email;
        $this->companyAuthenticationService->sendOtp($email);

        return $this->sendSuccessResponse(null, 'OTP sent successfully', Response::HTTP_OK);
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
     *
     *         @OA\JsonContent(
     *             required={"email","password","otp","name","address","documents"},
     *
     *             @OA\Property(property="email", type="string", format="email", example="company@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123"),
     *             @OA\Property(property="otp", type="string", example="123456"),
     *             @OA\Property(property="name", type="string", example="Acme Inc."),
     *             @OA\Property(property="phone", type="string", example="1234567890"),
     *             @OA\Property(property="website", type="string", example="https://acme.com"),
     *             @OA\Property(property="logo", type="string", example="https://acme.com/logo.png"),
     *             @OA\Property(property="description", type="string", example="Company description goes here."),
     *             @OA\Property(
     *                 property="address",
     *                 type="object",
     *                 required={"address_line1","city","country"},
     *                 @OA\Property(property="address_line1", type="string", example="123 Business St"),
     *                 @OA\Property(property="address_line2", type="string", example="Suite 100"),
     *                 @OA\Property(property="city", type="string", example="New York"),
     *                 @OA\Property(property="state", type="string", example="NY"),
     *                 @OA\Property(property="country", type="string", example="USA"),
     *                 @OA\Property(property="zip", type="string", example="10001"),
     *                 @OA\Property(property="is_primary", type="boolean", example=true)
     *             ),
     *             @OA\Property(
     *                 property="documents",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *                     required={"name","type","file_path","mime_type","issued_at","expires_at","is_verified"},
     *
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
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Company registered successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGci..."),
     *                 @OA\Property(property="token_type", type="string", example="bearer"),
     *                 @OA\Property(property="expires_in", type="integer", example=3600),
     *                 @OA\Property(property="company", ref="#/components/schemas/CompanyResource")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid credentials",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid credentials")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The email field is required.")
     *         )
     *     )
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        // check if otp is expired
        $passwordResetToken = PasswordResetTokens::where('token', $request->otp)
            ->where('email', $request->email)
            ->first();
        if (! $passwordResetToken || $passwordResetToken->expires_at < now()) {
            return $this->sendErrorResponse('OTP expired', Response::HTTP_BAD_REQUEST);
        }

        try {
            DB::beginTransaction();
            $company = $this->companyAuthenticationService->registerCompany($request->all());
            $this->companyAuthenticationService->registerWarehouse($company);

            CompanySetupSuccessfullyEvent::dispatch($company);

            $data = $request->all();
            $credentials = [
                'email' => $data['email'],
                'password' => $data['password'],
            ];

            if (! $token = Auth::guard('company')->attempt($credentials)) {
                return $this->sendErrorResponse('Invalid credentials', Response::HTTP_BAD_REQUEST);
            }

            $this->deviceService->registerDevice($company, $data);
            $this->companyAuthenticationService->deletePasswordResetToken($data['otp']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Company registered successfully',
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => Auth::guard('company')->factory()->getTTL() * 60,
                    'company' => CompanyDetailResource::make($company),
                ],
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendErrorResponse($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
     *
     *         @OA\JsonContent(
     *             required={"email","password"},
     *
     *             @OA\Property(property="email", type="string", format="email", example="company@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Company logged in successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Company logged in successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGci..."),
     *                 @OA\Property(property="token_type", type="string", example="bearer"),
     *                 @OA\Property(property="expires_in", type="integer", example=3600),
     *                 @OA\Property(property="company", ref="#/components/schemas/CompanyResource")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid credentials",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid credentials")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The email field is required.")
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        if (! $token = Auth::guard('company')->attempt($credentials)) {
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
            'holidays',
        ]);

        $this->deviceService->registerDevice($company, $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Company logged in successfully',
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => Auth::guard('company')->factory()->getTTL() * 60,
                'company' => CompanyDetailResource::make($company),
            ],
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/company/details",
     *     summary="Get company details",
     *     description="Fetches authenticated company's details including general settings, addresses, documents, warehouses, payment options, timings, holidays, etc.",
     *     tags={"Company Details"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Company details fetched successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Company details fetched successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/CompanyResource"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - invalid or missing token",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function details(): JsonResponse
    {
        $company = Auth::guard('company')->user()->load([
            'generalSettings',
            'addresses',
            'defaultAddress',
            'documents',
            'latestLocation',
            'warehouses',
            'paymentOptions',
            'timings',
            'holidays',
        ]);

        return $this->sendSuccessResponse(CompanyDetailResource::make($company), 'Company details fetched successfully', Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/company/auth/forgot-password",
     *     summary="Send OTP for password reset",
     *     description="Sends an OTP to the company's registered email address for password reset.",
     *     tags={"Company Authentication"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email"},
     *
     *             @OA\Property(property="email", type="string", format="email", example="company@example.com")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="OTP sent successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The email field is required.")
     *         )
     *     )
     * )
     */
    public function forgotPassword(ForgotPassword $request): JsonResponse
    {
        $this->companyAuthenticationService->sendOtp($request->email);

        return $this->sendSuccessResponse(null, 'OTP sent successfully', Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/company/auth/reset-password",
     *     summary="Reset password using OTP",
     *     description="Resets the company's password using the provided email, OTP, and new password.",
     *     tags={"Company Authentication"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email","password","otp"},
     *
     *             @OA\Property(property="email", type="string", format="email", example="company@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="newSecret123"),
     *             @OA\Property(property="otp", type="string", example="123456")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Password reset successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid or expired OTP",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid or expired OTP")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The email field is required.")
     *         )
     *     )
     * )
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $this->companyAuthenticationService->resetPassword($request->email, $request->password, $request->otp);

        return $this->sendSuccessResponse(null, 'Password reset successfully', Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/company/auth/update-password",
     *     summary="Update password for logged-in company",
     *     description="Allows an authenticated company to update its password by providing the old password and a new password.",
     *     tags={"Company Authentication"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"old_password","password","password_confirmation"},
     *
     *             @OA\Property(property="old_password", type="string", format="password", example="oldSecret123"),
     *             @OA\Property(property="password", type="string", format="password", example="newSecret123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="newSecret123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Password updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Password updated successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Old password does not match",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The old password is incorrect")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Missing or invalid token",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The password confirmation does not match.")
     *         )
     *     )
     * )
     */
    public function updatePassword(UpdatePassword $request): JsonResponse
    {
        $this->companyAuthenticationService->updatePassword($request->old_password, $request->password);

        return $this->sendSuccessResponse(null, 'Password updated successfully', Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/company/auth/logout",
     *     summary="Logout a company",
     *     description="Logs out a company and invalidates the access token.",
     *     tags={"Company Authentication"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Company logged out successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Company logged out successfully")
     *         )
     *     )
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $company = Auth::guard('company')->user();
        $this->deviceService->unregisterDevice($company, $request->all());
        Auth::guard('company')->logout();

        return $this->sendSuccessResponse(null, 'Company logged out successfully', Response::HTTP_OK);
    }
}
