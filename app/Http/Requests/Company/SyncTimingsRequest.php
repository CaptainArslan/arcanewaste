<?php

namespace App\Http\Requests\Company;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SyncTimingsRequest extends FormRequest
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
            'timings' => ['required', 'array', 'min:1'],
            'timings.*.day_of_week' => [
                'required',
                'string',
                Rule::in(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']),
                'distinct', // ensures no duplicate day_of_week values
            ],
            'timings.*.opens_at' => ['required', 'date_format:H:i:s'],
            'timings.*.closes_at' => ['required', 'date_format:H:i:s'],
            'timings.*.is_closed' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'timings.required' => 'The timings array is required.',
            'timings.array' => 'The timings must be an array.',
            'timings.*.day_of_week.required' => 'Day of week is required for each timing.',
            'timings.*.day_of_week.in' => 'Day of week must be one of monday, tuesday, wednesday, thursday, friday, saturday, sunday.',
            'timings.*.day_of_week.distinct' => 'Duplicate day_of_week values are not allowed.',
            'timings.*.opens_at.required' => 'Opening time is required.',
            'timings.*.opens_at.date_format' => 'Opening time must be in H:i:s format.',
            'timings.*.closes_at.required' => 'Closing time is required.',
            'timings.*.closes_at.date_format' => 'Closing time must be in H:i:s format.',
            'timings.*.is_closed.boolean' => 'Is closed must be a boolean value.',
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
