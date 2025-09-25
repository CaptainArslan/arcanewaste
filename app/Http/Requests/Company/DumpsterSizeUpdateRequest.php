<?php

namespace App\Http\Requests\Company;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class DumpsterSizeUpdateRequest extends FormRequest
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
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('dumpster_sizes', 'code')->ignore($this->route('dumpsterSize')->id),
            ],
            'description' => ['required', 'string', 'max:255'],
            'min_rental_days' => ['required', 'integer', 'min:1'],
            'max_rental_days' => ['required', 'integer', 'gte:min_rental_days'],
            'base_rent' => ['required', 'numeric', 'min:0'],
            'extra_day_rent' => ['required', 'numeric', 'min:0'],
            'overdue_rent' => ['required', 'numeric', 'min:0'],
            'volume_cubic_yards' => ['required', 'numeric', 'min:0'],
            'weight_limit_lbs' => ['required', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
            'taxes' => ['sometimes', 'array'],
            'taxes.*' => ['integer', 'exists:taxes,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'code.required' => 'The code field is required.',
            'description.required' => 'The description field is required.',
            'min_rental_days.required' => 'The min rental days field is required.',
            'min_rental_days.integer' => 'The min rental days field must be an integer.',
            'min_rental_days.min' => 'The min rental days field must be at least 1.',
            'max_rental_days.required' => 'The max rental days field is required.',
            'max_rental_days.integer' => 'The max rental days field must be an integer.',
            'max_rental_days.gte' => 'The max rental days field must be greater than or equal to the min rental days field.',
            'base_rent.required' => 'The base rent field is required.',
            'base_rent.numeric' => 'The base rent field must be a number.',
            'base_rent.min' => 'The base rent field must be at least 0.',
            'extra_day_rent.required' => 'The extra day rent field is required.',
            'extra_day_rent.numeric' => 'The extra day rent field must be a number.',
            'extra_day_rent.min' => 'The extra day rent field must be at least 0.',
            'overdue_rent.required' => 'The overdue rent field is required.',
            'overdue_rent.numeric' => 'The overdue rent field must be a number.',
            'overdue_rent.min' => 'The overdue rent field must be at least 0.',
            'volume_cubic_yards.required' => 'The volume cubic yards field is required.',
            'volume_cubic_yards.numeric' => 'The volume cubic yards field must be a number.',
            'volume_cubic_yards.min' => 'The volume cubic yards field must be at least 0.',
            'weight_limit_lbs.required' => 'The weight limit lbs field is required.',
            'weight_limit_lbs.integer' => 'The weight limit lbs field must be an integer.',
            'weight_limit_lbs.min' => 'The weight limit lbs field must be at least 0.',
            'is_active.required' => 'The is active field is required.',
            'is_active.boolean' => 'The is active field must be a boolean.',
            'taxes.sometimes' => 'The taxes field is required.',
            'taxes.*.integer' => 'The taxes field must be an integer.',
            'taxes.*.exists' => 'The taxes field must exist in the taxes table.',
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
