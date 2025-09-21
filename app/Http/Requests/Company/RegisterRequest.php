<?php

namespace App\Http\Requests\Company;

use App\Rules\AddressRule;
use App\Rules\DocumentRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:companies,email'],
            'password' => ['required', 'string', 'min:8'],
            'logo' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:65535'],
            'phone' => ['required', 'string', 'max:255'],
            'website' => ['required', 'string', 'max:255'],
            "address" => ['required', 'array', new AddressRule()],
            'documents' => ['required', 'array', new DocumentRule()],
            "device_token" => ['nullable', 'string',],
            'device_type' => ['required_if:device_token,not_null', 'in:android,ios', 'string',],
            'device_id' => ['required_if:device_token,not_null', 'string',],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email is invalid',
            'email.unique' => 'Email already exists',
            'password.required' => 'Password is required',
            'password.string' => 'Password must be a string',
            'password.min' => 'Password must be at least 8 characters',
            'logo.required' => 'Logo is required',
            'logo.string' => 'Logo must be a string',
            'logo.max' => 'Logo must be less than 255 characters',
            'description.required' => 'Description is required',
            'description.string' => 'Description must be a string',
            'description.max' => 'Description must be less than 255 characters',
            'phone.required' => 'Phone is required',
            'phone.string' => 'Phone must be a string',
            'phone.max' => 'Phone must be less than 255 characters',
            'website.required' => 'Website is required',
            'website.string' => 'Website must be a string',
            'website.max' => 'Website must be less than 255 characters',
        ];
    }

    /**
     * Handle failed validation.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' =>  implode(', ', $validator->errors()->all()),
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
