<?php

namespace App\Http\Requests\Company;

use App\Enums\GenderEnum;
use App\Rules\AddressRule;
use App\Rules\EmergencyContactRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class CustomerCreateRequest extends FormRequest
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
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:customers,email'],
            'phone' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'in:' . implode(',', GenderEnum::values())],
            'dob' => ['required', 'date'],
            'address' => ['required', 'array', new AddressRule],
            'emergency_contacts' => ['required', 'array', new EmergencyContactRule],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Full name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email is invalid',
            'email.unique' => 'Email already exists',
            'phone.required' => 'Phone is required',
            'phone.string' => 'Phone must be a string',
            'phone.max' => 'Phone must be less than 255 characters',
            'gender.required' => 'Gender is required',
            'gender.string' => 'Gender must be a string',
            'gender.in' => 'Gender must be either male, female or other',
            'dob.required' => 'Date of birth is required',
            'dob.date' => 'Date of birth must be a date',
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
