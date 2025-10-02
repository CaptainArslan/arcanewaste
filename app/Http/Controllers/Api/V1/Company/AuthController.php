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

    public function sendOtp(GetOtpRequest $request): JsonResponse
    {
        $email = $request->email;
        $this->companyAuthenticationService->sendOtp($email);

        return $this->sendSuccessResponse(null, 'OTP sent successfully', Response::HTTP_OK);
    }

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

    public function details(): JsonResponse
    {
        $company = Auth::guard('company')->user()->load([
            'generalSettings',
            'addresses',
            'defaultAddress',
            'documents',
            'warehouses',
            'paymentOptions',
            'timings',
            'holidays',
        ]);

        return $this->sendSuccessResponse(CompanyDetailResource::make($company), 'Company details fetched successfully', Response::HTTP_OK);
    }

    public function forgotPassword(ForgotPassword $request): JsonResponse
    {
        $this->companyAuthenticationService->sendOtp($request->email);

        return $this->sendSuccessResponse(null, 'OTP sent successfully', Response::HTTP_OK);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $this->companyAuthenticationService->resetPassword($request->email, $request->password, $request->otp);

        return $this->sendSuccessResponse(null, 'Password reset successfully', Response::HTTP_OK);
    }

    public function updatePassword(UpdatePassword $request): JsonResponse
    {
        $this->companyAuthenticationService->updatePassword($request->old_password, $request->password);

        return $this->sendSuccessResponse(null, 'Password updated successfully', Response::HTTP_OK);
    }

    public function logout(Request $request): JsonResponse
    {
        $company = Auth::guard('company')->user();
        $this->deviceService->unregisterDevice($company, $request->all());
        Auth::guard('company')->logout();

        return $this->sendSuccessResponse(null, 'Company logged out successfully', Response::HTTP_OK);
    }
}
