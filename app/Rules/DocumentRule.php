<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DocumentRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            $fail("The $attribute must be an array.");
            return;
        }

        foreach ($value as $document) {
            if (!is_array($document)) {
                $fail("The $attribute must be an array.");
                return;
            }
        }

        foreach ($value as $document) {
            if (!isset($document['name'])) {
                $fail("The $attribute.name field is required.");
            }
        }

        foreach ($value as $document) {
            if (!isset($document['type'])) {
                $fail("The $attribute.type field is required.");
            }
        }

        foreach ($value as $document) {
            if (!isset($document['file_path'])) {
                $fail("The $attribute.file_path field is required.");
            }
        }

        foreach ($value as $document) {
            if (!isset($document['mime_type'])) {
                $fail("The $attribute.mime_type field is required.");
            }
        }

        foreach ($value as $document) {
            if (!isset($document['issued_at'])) {
                $fail("The $attribute.issued_at field is required.");
            }
        }

        foreach ($value as $document) {
            if (!isset($document['expires_at'])) {
                $fail("The $attribute.expires_at field is required.");
            }
        }

        foreach ($value as $document) {
            if (!isset($document['is_verified'])) {
                $fail("The $attribute.is_verified field is required.");
            }
        }
    }
}
