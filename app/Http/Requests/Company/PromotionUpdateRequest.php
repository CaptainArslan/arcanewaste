<?php

namespace App\Http\Requests\Company;

use App\Enums\DiscountTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class PromotionUpdateRequest extends FormRequest
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
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'discount_type' => ['nullable', 'string', 'max:255', 'in:' . implode(',', DiscountTypeEnum::values())],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'dumpster_size_ids' => ['nullable', 'array'],
            'dumpster_size_ids.*' => [
                'nullable',
                'integer',
                Rule::exists('dumpster_sizes', 'id')->where(fn($q) => $q->where('company_id', $companyId)),
            ],
            'image' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.string' => 'Title must be a string',
            'title.max' => 'Title must be less than 255 characters',
            'description.string' => 'Description must be a string',
            'description.max' => 'Description must be less than 255 characters',
            'discount_type.string' => 'Discount type must be a string',
            'discount_type.max' => 'Discount type must be less than 255 characters',
            'discount_type.in' => 'Discount type must be in: ' . implode(',', DiscountTypeEnum::values()),
            'discount_value.numeric' => 'Discount value must be a number',
            'discount_value.min' => 'Discount value must be greater than 0',
            'start_date.date' => 'Start date must be a date',
            'end_date.date' => 'End date must be a date',
            'min_order_amount.numeric' => 'Min order amount must be a number',
            'min_order_amount.min' => 'Min order amount must be greater than 0',
            'usage_limit.integer' => 'Usage limit must be an integer',
            'usage_limit.min' => 'Usage limit must be greater than 0',
            'is_active.boolean' => 'Is active must be a boolean',
            'dumpster_size_ids.array' => 'Dumpster size ids must be an array',
            'dumpster_size_ids.*.integer' => 'Dumpster size id must be an integer',
            'dumpster_size_ids.*.exists' => 'Dumpster size id must exist in dumpster sizes table',
            'image.string' => 'Image must be a string',
            'image.max' => 'Image must be less than 255 characters',
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
