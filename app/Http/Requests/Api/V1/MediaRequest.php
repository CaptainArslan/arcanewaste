<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class MediaRequest extends FormRequest
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
            'media' => ['required', 'array', 'min:1'],
            'media.*' => ['file', 'max:2048'], // 2MB per file
            'remove_paths' => ['nullable', 'array'],
            'remove_paths.*' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'media.required' => 'At least one media file is required.',
            'media.*.file' => 'Each media item must be a valid file.',
            'media.*.max' => 'A media file may not be greater than 2048 kilobytes.',
        ];
    }

    publc function failedValidation(Validator $validator)
    {
        return $this->respondError($validator->errors()->first());
    }
}
