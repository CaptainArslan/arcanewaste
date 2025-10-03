<?php

namespace App\Http\Requests\Company;

use App\Enums\GenderEnum;
use App\Rules\AddressRule;
use App\Rules\EmergencyContactRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

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
        $companyId = Auth::guard('company')->id();

        return [
            'full_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'payment_option_id' => [
                'nullable',
                'exists:payment_options,id',
                Rule::exists('payment_options', 'id')->where('company_id', $companyId)
            ],
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
            'payment_option_id.exists' => 'Payment option does not exist',
            'payment_option_id.required' => 'Payment option is required',
            'payment_option_id.exists' => 'Payment option does not exist',
            'payment_option_id.required' => 'Payment option is required',
            'payment_option_id.exists' => 'Payment option does not exist',
            'dob.date' => 'Date of birth must be a date',
            'is_active.boolean' => 'Is active must be a boolean',
            'is_delinquent.boolean' => 'Is delinquent must be a boolean',
            'delinquent_days.integer' => 'Delinquent days must be an integer',
            'payment_option_id.exists' => 'Payment option does not exist',
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
