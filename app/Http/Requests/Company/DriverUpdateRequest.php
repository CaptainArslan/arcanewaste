<?php

namespace App\Http\Requests\Company;

use App\Enums\EmploymentTypeEnum;
use App\Enums\GenderEnum;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class DriverUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $driver = $this->route('driver') ?? null;

        return [
            'full_name' => [
                'nullable',
                'string',
                'max:255',
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('drivers', 'email')->ignore($driver?->id),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:255',
            ],
            'dob' => [
                'nullable',
                'date',
            ],
            'gender' => [
                'nullable',
                'string',
                'max:255',
                'in:'.implode(',', GenderEnum::values()),
            ],
            'image' => [
                'nullable',
                'string',
                'max:255',
            ],
            'license_number' => [
                'nullable',
                'string',
                'max:255',
            ],
            'license_expires_at' => [
                'nullable',
                'date',
            ],
            'identity_document' => [
                'nullable',
                'string',
                'max:255',
            ],
            'identity_expires_at' => [
                'nullable',
                'date',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
            'employment_type' => [
                'nullable',
                'string',
                'in:'.implode(',', EmploymentTypeEnum::values()),
            ],
            'hourly_rate' => [
                'nullable',
                'numeric',
            ],
            'duty_hours' => [
                'nullable',
                'numeric',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.string' => 'Full name must be a string',
            'full_name.max' => 'Full name must be less than 255 characters',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email must be unique',
            'phone.string' => 'Phone must be a string',
            'phone.max' => 'Phone must be less than 255 characters',
            'dob.date' => 'Date of birth must be a date',
            'gender.string' => 'Gender must be a string',
            'gender.max' => 'Gender must be less than 255 characters',
            'image.string' => 'Image must be a string',
            'image.max' => 'Image must be less than 255 characters',
            'license_number.string' => 'License number must be a string',
            'license_number.max' => 'License number must be less than 255 characters',
            'license_expires_at.date' => 'License expires at must be a date',
            'identity_document.string' => 'Identity document must be a string',
            'identity_document.max' => 'Identity document must be less than 255 characters',
            'identity_expires_at.date' => 'Identity expires at must be a date',
            'is_active.boolean' => 'Is active must be a boolean',
            'employment_type.string' => 'Employment type must be a string',
            'employment_type.in' => 'Employment type must be in: '.implode(',', EmploymentTypeEnum::values()),
            'hourly_rate.numeric' => 'Hourly rate must be a number',
            'duty_hours.numeric' => 'Duty hours must be a number',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => implode(', ', $validator->errors()->all()),
                'errors' => $validator->errors()->all(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
