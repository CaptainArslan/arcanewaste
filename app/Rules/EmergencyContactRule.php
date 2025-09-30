<?php

namespace App\Rules;

use App\Enums\EmergencyContactTypeEnum;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EmergencyContactRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_array($value)) {
            $fail("The $attribute must be an array of contacts.");

            return;
        }

        foreach ($value as $index => $contact) {
            $prefix = "$attribute.$index";

            if (! is_array($contact)) {
                $fail("Each contact in $attribute must be an array.");

                continue;
            }

            // Validate required fields
            if (empty($contact['name']) || ! is_string($contact['name'])) {
                $fail("The $prefix.name field is required and must be a string.");
            }

            if (empty($contact['phone']) || ! is_string($contact['phone'])) {
                $fail("The $prefix.phone field is required and must be a string.");
            }

            // relation is optional but must be string if provided
            if (isset($contact['relation']) && ! is_string($contact['relation'])) {
                $fail("The $prefix.relation field must be a string.");
            }

            // type validation against enum
            if (empty($contact['type']) || ! in_array($contact['type'], array_column(EmergencyContactTypeEnum::cases(), 'value'))) {
                $fail("The $prefix.type field is required and must be one of: ".implode(', ', array_column(EmergencyContactTypeEnum::cases(), 'value')).'.');
            }
        }
    }
}
