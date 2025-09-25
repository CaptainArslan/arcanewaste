<?php

use Illuminate\Http\Request;
use App\Http\Middleware\VerifyJwt;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckJsonHeaders;
use App\Http\Controllers\Api\V1\MediaController;
use App\Http\Controllers\Api\V1\PaymentMethodController;
use App\Http\Controllers\Api\V1\Company\PaymentOptionController;
use App\Http\Controllers\Api\V1\Company\GeneralSettingController;
use App\Http\Controllers\Api\V1\Company\AuthController as CompanyAuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::prefix('v1')->group(function () {
    Route::post('media/upload', [MediaController::class, 'store']);

    // Payment Methods Routes
    // Route::get('payment-methods', [PaymentMethodController::class, 'index']);
    // Route::get('payment-methods/{code}', [PaymentMethodController::class, 'show']);
    // Route::get('payment-methods/{code}/onboarding-requirements', [PaymentMethodController::class, 'onboardingRequirements']);

    // Company Routes
    Route::prefix('company')->group(function () {
        Route::middleware([CheckJsonHeaders::class])
            ->prefix('auth')->group(function () {
                Route::post('send-otp', [CompanyAuthController::class, 'sendOtp']);
                Route::post('register', [CompanyAuthController::class, 'register']);
                Route::post('login', [CompanyAuthController::class, 'login']);
                Route::post('forgot-password', [CompanyAuthController::class, 'forgotPassword']);
                Route::post('reset-password', [CompanyAuthController::class, 'resetPassword']);
                Route::middleware([VerifyJwt::class])->group(function () {
                    Route::post('update-password', [CompanyAuthController::class, 'updatePassword']);
                    Route::post('logout', [CompanyAuthController::class, 'logout']);
                });
            });

        Route::middleware([VerifyJwt::class, CheckJsonHeaders::class])->group(function () {
            Route::get('details', [CompanyAuthController::class, 'details']);

            // General Settings Routes
            Route::get('general-settings', [GeneralSettingController::class, 'index']);
            Route::get('general-settings/{generalSetting}', [GeneralSettingController::class, 'show']);
            Route::put('general-settings/{generalSetting}/{key}', [GeneralSettingController::class, 'update']);

            // Payment Options Routes
            Route::get('payment-options', [PaymentOptionController::class, 'index']);
            Route::get('payment-options/{paymentOption}', [PaymentOptionController::class, 'show']);
            Route::put('payment-options/{paymentOption}/{type}', [PaymentOptionController::class, 'update']);
        });
    });
});
