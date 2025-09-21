<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AddressRule implements ValidationRule
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

        // Required fields
        $required = ['address_line1', 'city', 'country'];

        foreach ($required as $field) {
            if (empty($value[$field])) {
                $fail("The $attribute.$field field is required.");
            }
        }

        // Max length check
        foreach ($value as $field => $val) {
            if (!is_null($val) && is_string($val) && strlen($val) > 255) {
                $fail("The $attribute.$field may not be greater than 255 characters.");
            }
        }

        // Boolean check
        if (isset($value['is_primary']) && !is_bool($value['is_primary'])) {
            $fail("The $attribute.is_primary must be a boolean.");
        }
    }
}
