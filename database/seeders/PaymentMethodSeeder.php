<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            [
                'name' => 'Finix',
                'code' => 'finix',
                'description' => 'Finix is a payment infrastructure platform that helps businesses accept payments online and in-person.',
                'logo_url' => 'payment-logos/finix.png',
                'website_url' => 'https://finix.com',
                'supported_countries' => ['US', 'CA'],
                'supported_currencies' => ['USD', 'CAD'],
                'supported_payment_types' => ['credit_card', 'debit_card', 'bank_transfer'],
                'transaction_fee_percentage' => 0.029,
                'transaction_fee_fixed' => 30, // $0.30 in cents
                'monthly_fee' => 0, // $0.00 in cents
                'setup_fee' => 0, // $0.00 in cents
                'min_transaction_amount' => 50, // $0.50
                'max_transaction_amount' => 10000000, // $100,000
                'api_configuration' => [
                    'base_url' => 'https://api.finix.com',
                    'sandbox_url' => 'https://api-sandbox.finix.com',
                    'auth_type' => 'bearer_token',
                    'webhook_supported' => true,
                    'api_version' => 'v1',
                ],
                'onboarding_requirements' => [
                    'business_license',
                    'tax_id',
                    'bank_account_info',
                    'business_address',
                    'contact_information',
                    'identity_verification',
                ],
                'features' => [
                    'recurring_payments',
                    'refunds',
                    'chargebacks',
                    'multi_currency',
                    'webhooks',
                    'reporting',
                    'fraud_protection',
                ],
                'status' => 'active',
                'is_popular' => true,
                'requires_merchant_onboarding' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Stripe',
                'code' => 'stripe',
                'description' => 'Stripe is a technology company that builds economic infrastructure for the internet.',
                'logo_url' => 'payment-logos/stripe.png',
                'website_url' => 'https://stripe.com',
                'supported_countries' => ['US', 'CA', 'GB', 'AU', 'DE', 'FR', 'IT', 'ES', 'NL', 'BE', 'AT', 'CH', 'SE', 'NO', 'DK', 'FI', 'IE', 'PT', 'LU', 'MT', 'CY', 'EE', 'LV', 'LT', 'SI', 'SK', 'CZ', 'HU', 'PL', 'RO', 'BG', 'HR', 'GR'],
                'supported_currencies' => ['USD', 'CAD', 'GBP', 'EUR', 'AUD', 'CHF', 'SEK', 'NOK', 'DKK', 'PLN', 'CZK', 'HUF', 'RON', 'BGN', 'HRK'],
                'supported_payment_types' => ['credit_card', 'debit_card', 'bank_transfer', 'digital_wallet', 'buy_now_pay_later'],
                'transaction_fee_percentage' => 0.029,
                'transaction_fee_fixed' => 30, // $0.30 in cents
                'monthly_fee' => 0, // $0.00 in cents
                'setup_fee' => 0, // $0.00 in cents
                'min_transaction_amount' => 50,
                'max_transaction_amount' => null,
                'api_configuration' => [
                    'base_url' => 'https://api.stripe.com',
                    'sandbox_url' => 'https://api.stripe.com',
                    'auth_type' => 'api_key',
                    'webhook_supported' => true,
                    'api_version' => '2023-10-16',
                ],
                'onboarding_requirements' => [
                    'business_license',
                    'tax_id',
                    'bank_account_info',
                    'business_address',
                    'contact_information',
                    'identity_verification',
                    'website_url',
                ],
                'features' => [
                    'recurring_payments',
                    'refunds',
                    'chargebacks',
                    'multi_currency',
                    'webhooks',
                    'reporting',
                    'fraud_protection',
                    'subscription_billing',
                    'marketplace_payments',
                    'connect_platform',
                ],
                'status' => 'active',
                'is_popular' => true,
                'requires_merchant_onboarding' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'PayPal',
                'code' => 'paypal',
                'description' => 'PayPal is a multinational financial technology company operating an online payments system.',
                'logo_url' => 'payment-logos/paypal.png',
                'website_url' => 'https://paypal.com',
                'supported_countries' => ['US', 'CA', 'GB', 'AU', 'DE', 'FR', 'IT', 'ES', 'NL', 'BE', 'AT', 'CH', 'SE', 'NO', 'DK', 'FI', 'IE', 'PT', 'LU', 'MT', 'CY', 'EE', 'LV', 'LT', 'SI', 'SK', 'CZ', 'HU', 'PL', 'RO', 'BG', 'HR', 'GR', 'BR', 'MX', 'AR', 'CL', 'CO', 'PE', 'UY', 'VE', 'JP', 'KR', 'SG', 'HK', 'TW', 'MY', 'TH', 'PH', 'ID', 'VN', 'IN', 'NZ', 'ZA', 'IL', 'AE', 'SA', 'KW', 'QA', 'BH', 'OM', 'JO', 'LB', 'EG', 'MA', 'TN', 'DZ', 'LY', 'SD', 'ET', 'KE', 'NG', 'GH', 'ZA', 'ZW', 'BW', 'NA', 'ZM', 'MW', 'MZ', 'MG', 'MU', 'SC', 'RE', 'YT'],
                'supported_currencies' => ['USD', 'CAD', 'GBP', 'EUR', 'AUD', 'CHF', 'SEK', 'NOK', 'DKK', 'PLN', 'CZK', 'HUF', 'RON', 'BGN', 'HRK', 'BRL', 'MXN', 'ARS', 'CLP', 'COP', 'PEN', 'UYU', 'VES', 'JPY', 'KRW', 'SGD', 'HKD', 'TWD', 'MYR', 'THB', 'PHP', 'IDR', 'VND', 'INR', 'NZD', 'ZAR', 'ILS', 'AED', 'SAR', 'KWD', 'QAR', 'BHD', 'OMR', 'JOD', 'LBP', 'EGP', 'MAD', 'TND', 'DZD', 'LYD', 'SDG', 'ETB', 'KES', 'NGN', 'GHS', 'ZWL', 'BWP', 'NAD', 'ZMW', 'MWK', 'MZN', 'MGA', 'MUR', 'SCR', 'EUR'],
                'supported_payment_types' => ['credit_card', 'debit_card', 'digital_wallet', 'bank_transfer'],
                'transaction_fee_percentage' => 0.034,
                'transaction_fee_fixed' => 0.00,
                'monthly_fee' => 0, // $0.00 in cents
                'setup_fee' => 0, // $0.00 in cents
                'min_transaction_amount' => 100,
                'max_transaction_amount' => null,
                'api_configuration' => [
                    'base_url' => 'https://api.paypal.com',
                    'sandbox_url' => 'https://api.sandbox.paypal.com',
                    'auth_type' => 'oauth2',
                    'webhook_supported' => true,
                    'api_version' => 'v2',
                ],
                'onboarding_requirements' => [
                    'business_license',
                    'tax_id',
                    'bank_account_info',
                    'business_address',
                    'contact_information',
                    'identity_verification',
                    'website_url',
                ],
                'features' => [
                    'recurring_payments',
                    'refunds',
                    'chargebacks',
                    'multi_currency',
                    'webhooks',
                    'reporting',
                    'fraud_protection',
                    'subscription_billing',
                    'marketplace_payments',
                    'express_checkout',
                ],
                'status' => 'active',
                'is_popular' => true,
                'requires_merchant_onboarding' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Square',
                'code' => 'square',
                'description' => 'Square is a financial services and digital payments company.',
                'logo_url' => 'payment-logos/square.png',
                'website_url' => 'https://squareup.com',
                'supported_countries' => ['US', 'CA', 'GB', 'AU', 'FR', 'ES', 'IE'],
                'supported_currencies' => ['USD', 'CAD', 'GBP', 'AUD', 'EUR'],
                'supported_payment_types' => ['credit_card', 'debit_card', 'digital_wallet'],
                'transaction_fee_percentage' => 0.026,
                'transaction_fee_fixed' => 0.10,
                'monthly_fee' => 0, // $0.00 in cents
                'setup_fee' => 0, // $0.00 in cents
                'min_transaction_amount' => 100,
                'max_transaction_amount' => null,
                'api_configuration' => [
                    'base_url' => 'https://connect.squareup.com',
                    'sandbox_url' => 'https://connect.squareupsandbox.com',
                    'auth_type' => 'oauth2',
                    'webhook_supported' => true,
                    'api_version' => '2023-10-18',
                ],
                'onboarding_requirements' => [
                    'business_license',
                    'tax_id',
                    'bank_account_info',
                    'business_address',
                    'contact_information',
                    'identity_verification',
                ],
                'features' => [
                    'recurring_payments',
                    'refunds',
                    'chargebacks',
                    'multi_currency',
                    'webhooks',
                    'reporting',
                    'fraud_protection',
                    'subscription_billing',
                    'invoicing',
                    'inventory_management',
                ],
                'status' => 'active',
                'is_popular' => true,
                'requires_merchant_onboarding' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Razorpay',
                'code' => 'razorpay',
                'description' => 'Razorpay is a payment gateway and financial technology company.',
                'logo_url' => 'payment-logos/razorpay.png',
                'website_url' => 'https://razorpay.com',
                'supported_countries' => ['IN', 'AE', 'SG'],
                'supported_currencies' => ['INR', 'AED', 'SGD'],
                'supported_payment_types' => ['credit_card', 'debit_card', 'bank_transfer', 'digital_wallet', 'upi'],
                'transaction_fee_percentage' => 0.020,
                'transaction_fee_fixed' => 0.00,
                'monthly_fee' => 0, // $0.00 in cents
                'setup_fee' => 0, // $0.00 in cents
                'min_transaction_amount' => 100,
                'max_transaction_amount' => null,
                'api_configuration' => [
                    'base_url' => 'https://api.razorpay.com',
                    'sandbox_url' => 'https://api.razorpay.com',
                    'auth_type' => 'basic_auth',
                    'webhook_supported' => true,
                    'api_version' => 'v1',
                ],
                'onboarding_requirements' => [
                    'business_license',
                    'tax_id',
                    'bank_account_info',
                    'business_address',
                    'contact_information',
                    'identity_verification',
                    'website_url',
                ],
                'features' => [
                    'recurring_payments',
                    'refunds',
                    'chargebacks',
                    'multi_currency',
                    'webhooks',
                    'reporting',
                    'fraud_protection',
                    'subscription_billing',
                    'marketplace_payments',
                    'upi_payments',
                ],
                'status' => 'active',
                'is_popular' => false,
                'requires_merchant_onboarding' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::create($method);
        }
    }
}
