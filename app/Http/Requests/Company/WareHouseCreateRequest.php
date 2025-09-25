<?php

namespace App\Http\Requests\Company;

use App\Rules\AddressRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;

class WareHouseCreateRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:255', 'unique:warehouses,code'],
            'type' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer'],
            'is_active' => ['required', 'boolean'],
            'address' => ['required', 'array', new AddressRule()],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'code.required' => 'Code is required',
            'code.unique' => 'Code already exists',
            'type.required' => 'Type is required',
            'capacity.required' => 'Capacity is required',
            'is_active.required' => 'Is active is required',
            'address.required' => 'Address is required',
            'address.array' => 'Address must be an array',
            'address.address_line1.required' => 'Address line 1 is required',
            'address.address_line2.required' => 'Address line 2 is required',
            'address.city.required' => 'City is required',
            'address.country.required' => 'Country is required',
            'address.is_primary.required' => 'Is primary is required',
            'address.is_primary.boolean' => 'Is primary must be a boolean',
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
