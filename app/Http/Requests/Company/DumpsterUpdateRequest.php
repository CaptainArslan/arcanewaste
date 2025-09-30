<?php

namespace App\Http\Requests\Company;

use App\Enums\DumpsterStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DumpsterUpdateRequest extends FormRequest
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
            'slug' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', Rule::in(DumpsterStatusEnum::values())],
            'image' => ['nullable', 'string'],
            'last_service_date' => ['nullable', 'date'],
            'next_service_due' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'is_available' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
