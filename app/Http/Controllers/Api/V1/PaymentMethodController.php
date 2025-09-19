<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class PaymentMethodController extends Controller
{
    /**
     * Get all active payment methods
     */
    public function index(Request $request): JsonResponse
    {
        $query = PaymentMethod::active()->ordered();

        // Filter by country if provided
        if ($request->has('country')) {
            $query->where(function ($q) use ($request) {
                $q->whereJsonContains('supported_countries', $request->country)
                  ->orWhereNull('supported_countries');
            });
        }

        // Filter by currency if provided
        if ($request->has('currency')) {
            $query->where(function ($q) use ($request) {
                $q->whereJsonContains('supported_currencies', $request->currency)
                  ->orWhereNull('supported_currencies');
            });
        }

        // Filter by payment type if provided
        if ($request->has('payment_type')) {
            $query->whereJsonContains('supported_payment_types', $request->payment_type);
        }

        // Show only popular ones if requested
        if ($request->boolean('popular_only')) {
            $query->popular();
        }

        $paymentMethods = $query->get();

        // Transform the data for API response
        $transformedMethods = $paymentMethods->map(function ($method) {
            return [
                'id' => $method->id,
                'name' => $method->name,
                'slug' => $method->slug,
                'description' => $method->description,
                'logo_url' => $method->logo_url,
                'website_url' => $method->website_url,
                'supported_countries' => $method->supported_countries,
                'supported_currencies' => $method->supported_currencies,
                'supported_payment_types' => $method->supported_payment_types,
                'transaction_fee_formatted' => $method->formatted_transaction_fee,
                'monthly_fee_formatted' => $method->formatted_monthly_fee,
                'setup_fee_formatted' => $method->formatted_setup_fee,
                'min_transaction_amount' => $method->min_transaction_amount,
                'max_transaction_amount' => $method->max_transaction_amount,
                'features' => $method->features,
                'is_popular' => $method->is_popular,
                'requires_merchant_onboarding' => $method->requires_merchant_onboarding,
                'onboarding_requirements' => $method->getOnboardingRequirements(),
            ];
        });

        return $this->sendSuccessResponse($transformedMethods, 'Payment methods retrieved successfully.');
    }

    /**
     * Get a specific payment method by slug
     */
    public function show(string $slug): JsonResponse
    {
        $paymentMethod = PaymentMethod::active()->where('slug', $slug)->first();

        if (!$paymentMethod) {
            return $this->sendErrorResponse('Payment method not found.', 404);
        }

        $transformedMethod = [
            'id' => $paymentMethod->id,
            'name' => $paymentMethod->name,
            'slug' => $paymentMethod->slug,
            'description' => $paymentMethod->description,
            'logo_url' => $paymentMethod->logo_url,
            'website_url' => $paymentMethod->website_url,
            'supported_countries' => $paymentMethod->supported_countries,
            'supported_currencies' => $paymentMethod->supported_currencies,
            'supported_payment_types' => $paymentMethod->supported_payment_types,
            'transaction_fee_percentage' => $paymentMethod->transaction_fee_percentage,
            'transaction_fee_fixed' => $paymentMethod->transaction_fee_fixed,
            'transaction_fee_formatted' => $paymentMethod->formatted_transaction_fee,
            'monthly_fee' => $paymentMethod->monthly_fee,
            'monthly_fee_formatted' => $paymentMethod->formatted_monthly_fee,
            'setup_fee' => $paymentMethod->setup_fee,
            'setup_fee_formatted' => $paymentMethod->formatted_setup_fee,
            'min_transaction_amount' => $paymentMethod->min_transaction_amount,
            'max_transaction_amount' => $paymentMethod->max_transaction_amount,
            'features' => $paymentMethod->features,
            'is_popular' => $paymentMethod->is_popular,
            'requires_merchant_onboarding' => $paymentMethod->requires_merchant_onboarding,
            'onboarding_requirements' => $paymentMethod->getOnboardingRequirements(),
            'api_configuration' => $paymentMethod->getApiConfiguration(),
        ];

        return $this->sendSuccessResponse($transformedMethod, 'Payment method retrieved successfully.');
    }

    /**
     * Get onboarding requirements for a specific payment method
     */
    public function onboardingRequirements(string $slug): JsonResponse
    {
        $paymentMethod = PaymentMethod::active()->where('slug', $slug)->first();

        if (!$paymentMethod) {
            return $this->sendErrorResponse('Payment method not found.', 404);
        }

        if (!$paymentMethod->requires_merchant_onboarding) {
            return $this->sendErrorResponse('This payment method does not require merchant onboarding.', 400);
        }

        $requirements = [
            'payment_method' => [
                'name' => $paymentMethod->name,
                'slug' => $paymentMethod->slug,
                'logo_url' => $paymentMethod->logo_url,
            ],
            'requirements' => $paymentMethod->getOnboardingRequirements(),
            'api_configuration' => $paymentMethod->getApiConfiguration(),
            'estimated_setup_time' => $this->getEstimatedSetupTime($paymentMethod->slug),
            'next_steps' => $this->getNextSteps($paymentMethod->slug),
        ];

        return $this->sendSuccessResponse($requirements, 'Onboarding requirements retrieved successfully.');
    }

    /**
     * Get estimated setup time for a payment method
     */
    private function getEstimatedSetupTime(string $slug): string
    {
        $setupTimes = [
            'finix' => '1-3 business days',
            'stripe' => 'Same day',
            'paypal' => '1-2 business days',
            'square' => 'Same day',
            'razorpay' => '1-2 business days',
        ];

        return $setupTimes[$slug] ?? '2-5 business days';
    }

    /**
     * Get next steps for onboarding
     */
    private function getNextSteps(string $slug): array
    {
        $nextSteps = [
            'finix' => [
                'Complete merchant application',
                'Provide business documentation',
                'Verify bank account information',
                'Complete identity verification',
                'Wait for approval',
            ],
            'stripe' => [
                'Create Stripe account',
                'Complete business information',
                'Add bank account details',
                'Verify identity',
                'Start accepting payments',
            ],
            'paypal' => [
                'Create PayPal Business account',
                'Complete business verification',
                'Add bank account',
                'Verify email and phone',
                'Activate account',
            ],
            'square' => [
                'Create Square account',
                'Complete business profile',
                'Add bank account',
                'Verify identity',
                'Start processing payments',
            ],
            'razorpay' => [
                'Create Razorpay account',
                'Complete KYC process',
                'Add bank account details',
                'Verify business documents',
                'Activate payment gateway',
            ],
        ];

        return $nextSteps[$slug] ?? [
            'Complete application',
            'Provide required documents',
            'Verify information',
            'Wait for approval',
            'Start processing payments',
        ];
    }
}
