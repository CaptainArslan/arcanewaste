<?php

namespace App\Http\Requests\Company;

use App\Rules\AddressRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class WareHouseUpdateRequest extends FormRequest
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
            'name' => ['nullable', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255', Rule::unique('warehouses')->ignore($this->warehouse->id)],
            'type' => ['nullable', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
            'address' => ['nullable', 'array', new AddressRule],
        ];
    }

    public function messages(): array
    {
        return [
            'code.unique' => 'Code already exists',
            'is_active.nullable' => 'Is active is required',
            'address.array' => 'Address must be an array',
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
