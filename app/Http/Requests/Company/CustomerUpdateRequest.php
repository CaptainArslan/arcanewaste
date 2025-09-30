<?php

namespace App\Http\Requests\Company;

use App\Enums\GenderEnum;
use App\Rules\AddressRule;
use App\Rules\EmergencyContactRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class CustomerUpdateRequest extends FormRequest
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
            'full_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'is_delinquent' => ['nullable', 'boolean'],
            'delinquent_days' => ['nullable', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.string' => 'Full name must be a string',
            'full_name.max' => 'Full name must be less than 255 characters',
            'phone.string' => 'Phone must be a string',
            'phone.max' => 'Phone must be less than 255 characters',
            'dob.date' => 'Date of birth must be a date',
            'is_active.boolean' => 'Is active must be a boolean',
            'is_delinquent.boolean' => 'Is delinquent must be a boolean',
            'delinquent_days.integer' => 'Delinquent days must be an integer',
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
