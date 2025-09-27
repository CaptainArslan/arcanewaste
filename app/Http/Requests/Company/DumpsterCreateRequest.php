<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class DumpsterCreateRequest extends FormRequest
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
            'name'             => ['required', 'string', 'max:255'],
            'company_id'       => ['required', 'exists:companies,id'],
            'dumpster_size_id' => ['required', 'exists:dumpster_sizes,id'],
            'warehouse_id'     => ['nullable', 'exists:warehouses,id'],

            'serial_number'    => ['nullable', 'string', 'max:255', 'unique:dumpsters,serial_number'],
            'status'           => ['nullable', 'string', 'in:available,rented,maintenance,inactive'],

            'last_service_date' => ['nullable', 'date'],
            'next_service_due'  => ['nullable', 'date'],

            'notes'        => ['nullable', 'string'],
            'is_available' => ['nullable', 'boolean'],
            'is_active'    => ['nullable', 'boolean'],
        ];
    }


    public function messages(): array
    {
        return [
            'company_id.required' => 'The company id field is required.',
            'company_id.exists' => 'The company id field must exist in the companies table.',
            'dumpster_size_id.required' => 'The dumpster size id field is required.',
            'dumpster_size_id.exists' => 'The dumpster size id field must exist in the dumpster sizes table.',
            'warehouse_id.exists' => 'The warehouse id field must exist in the warehouses table.',
            'serial_number.unique' => 'The serial number field must be unique.',
            'status.in' => 'The status field must be either available, rented, maintenance, or inactive.',
            'last_service_date.date' => 'The last service date field must be a date.',
            'next_service_due.date' => 'The next service due field must be a date.',
            'notes.string' => 'The notes field must be a string.',
            'is_available.boolean' => 'The is available field must be a boolean.',
            'is_active.boolean' => 'The is active field must be a boolean.',
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
