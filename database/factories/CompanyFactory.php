<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'customer_panel_url' => $this->faker->url(),
            'logo' => $this->faker->imageUrl(),
            'description' => $this->faker->sentence(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->email(),
            'password' => $this->faker->password(),
            'website' => $this->faker->url(),
            'onboarding_status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'finix_identity_id' => $this->faker->uuid(),
            'finix_merchant_id' => $this->faker->uuid(),
            'finix_onboarding_form_id' => $this->faker->uuid(),
            'finix_onboarding_url' => $this->faker->url(),
            'finix_onboarding_url_expired_at' => $this->faker->dateTime(),
            'finix_onboarding_status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'finix_onboarding_notes' => $this->faker->sentence(),
            'finix_onboarding_completed_at' => $this->faker->dateTime(),
            'is_active' => $this->faker->boolean(),

        ];
    }
}
