<?php

namespace App\Http\Requests\Company;

use App\Enums\EmploymentTypeEnum;
use App\Enums\GenderEnum;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class DriverCreateRequest extends FormRequest
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
        return [
            'full_name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
            ],
            'phone' => [
                'required',
                'string',
                'max:255',
            ],
            'dob' => [
                'required',
                'date',
            ],
            'gender' => [
                'required',
                'string',
                'max:255',
                'in:'.implode(',', GenderEnum::values()),
            ],
            'image' => [
                'string',
                'max:255',
            ],
            'license_number' => [
                'required',
                'string',
                'max:255',
            ],
            'license_expires_at' => [
                'required',
                'date',
            ],
            'identity_document' => [
                'required',
                'string',
                'max:255',
            ],
            'identity_expires_at' => [
                'required',
                'date',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
            'employment_type' => [
                'required',
                'string',
                'in:'.implode(',', EmploymentTypeEnum::values()),
            ],
            'hourly_rate' => [
                'required',
                'numeric',
                'min:0',
            ],
            'duty_hours' => [
                'required',
                'numeric',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Full name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email is invalid',
            'phone.required' => 'Phone is required',
            'phone.string' => 'Phone must be a string',
            'phone.max' => 'Phone must be less than 255 characters',
            'dob.required' => 'Date of birth is required',
            'dob.date' => 'Date of birth must be a date',
            'gender.required' => 'Gender is required',
            'gender.string' => 'Gender must be a string',
            'gender.max' => 'Gender must be less than 255 characters',
            'image.required' => 'Profile picture is required',
            'image.string' => 'Profile picture must be a string',
            'image.max' => 'Profile picture must be less than 255 characters',
            'license_number.required' => 'License number is required',
            'license_number.string' => 'License number must be a string',
            'license_number.max' => 'License number must be less than 255 characters',
            'license_expires_at.required' => 'License expires at is required',
            'license_expires_at.date' => 'License expires at must be a date',
            'identity_document.required' => 'Identity document is required',
            'identity_document.string' => 'Identity document must be a string',
            'identity_document.max' => 'Identity document must be less than 255 characters',
            'identity_expires_at.required' => 'Identity expires at is required',
            'identity_expires_at.date' => 'Identity expires at must be a date',
            'is_active.required' => 'Is active is required',
            'is_active.boolean' => 'Is active must be a boolean',
            'employment_type.required' => 'Employment type is required',
            'employment_type.string' => 'Employment type must be a string',
            'employment_type.in' => 'Employment type must be in: '.implode(',', EmploymentTypeEnum::values()),
            'hourly_rate.required' => 'Hourly rate is required',
            'hourly_rate.numeric' => 'Hourly rate must be a number',
            'hourly_rate.min' => 'Hourly rate must be greater than 0',
            'duty_hours.required' => 'Duty hours is required',
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
