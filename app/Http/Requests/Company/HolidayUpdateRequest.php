<?php

namespace App\Http\Requests\Company;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class HolidayUpdateRequest extends FormRequest
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
            'date' => ['required', 'date'],
            'recurrence_type' => ['nullable', 'string', 'in:none,weekly,yearly'],
            'day_of_week' => ['nullable', 'integer', 'min:0', 'max:6'],
            'month_day' => ['nullable', 'string', 'max:5'],
            'reason' => ['nullable', 'string', 'max:255'],
            'is_approved' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'date.required' => 'The date field is required.',
            'recurrence_type.nullable' => 'The recurrence type field is required.',
            'recurrence_type.string' => 'The recurrence type field must be a string.',
            'recurrence_type.in' => 'The recurrence type field must be one of none, weekly, yearly.',
            'day_of_week.nullable' => 'The day of week field is required.',
            'day_of_week.integer' => 'The day of week field must be an integer.',
            'day_of_week.min' => 'The day of week field must be greater than 0.',
            'day_of_week.max' => 'The day of week field must be less than 6.',
            'month_day.nullable' => 'The month day field is required.',
            'month_day.string' => 'The month day field must be a string.',
            'month_day.max' => 'The month day field must be less than 5 characters.',
            'reason.nullable' => 'The reason field is required.',
            'reason.string' => 'The reason field must be a string.',
            'reason.max' => 'The reason field must be less than 255 characters.',
        ];
    }


    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $recurrence = $this->input('recurrence_type');
            $dayOfWeek  = $this->input('day_of_week');
            $date       = $this->input('date');
            $monthDay   = $this->input('month_day');

            // Weekly requires day_of_week
            if ($recurrence === 'weekly' && is_null($dayOfWeek)) {
                $validator->errors()->add('day_of_week', 'Day of week is required for weekly recurrence.');
            }

            // Yearly requires month_day
            if ($recurrence === 'yearly' && is_null($monthDay)) {
                $validator->errors()->add('month_day', 'Month-Day is required for yearly recurrence.');
            }

            // If both date and day_of_week provided, they must match
            if ($date && !is_null($dayOfWeek)) {
                $carbonDate = Carbon::parse($date);
                if ($carbonDate->dayOfWeek !== (int) $dayOfWeek) {
                    $validator->errors()->add(
                        'day_of_week',
                        "The provided day_of_week ({$dayOfWeek}) does not match the date {$carbonDate->toDateString()} ({$carbonDate->format('l')})."
                    );
                }
            }
        });
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
