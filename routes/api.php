<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\MediaController;
use App\Http\Controllers\Api\V1\Company\AuthController as CompanyAuthController;
use App\Http\Controllers\Api\V1\PaymentMethodController;
use App\Http\Middleware\CheckJsonHeaders;

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
        Route::middleware([CheckJsonHeaders::class])->prefix('auth')->group(function () {
            Route::post('register', [CompanyAuthController::class, 'register']);
        });
    });
});
