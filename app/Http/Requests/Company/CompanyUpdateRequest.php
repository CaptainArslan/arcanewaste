<?php

namespace App\Http\Requests\Company;

use App\Rules\AddressRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class CompanyUpdateRequest extends FormRequest
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
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', Rule::unique('companies', 'email')->ignore($companyId)],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'array', new AddressRule],
            'logo' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
            'website' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Email already exists',
            'address.array' => 'Address must be an array',
            'logo.string' => 'Logo must be a string',
            'logo.max' => 'Logo must be less than 255 characters',
            'description.string' => 'Description must be a string',
            'description.max' => 'Description must be less than 65535 characters',
            'website.string' => 'Website must be a string',
            'website.max' => 'Website must be less than 255 characters',
            'is_active.boolean' => 'Is active must be a boolean',
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
