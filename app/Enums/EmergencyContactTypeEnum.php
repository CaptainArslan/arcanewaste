<?php

namespace App\Enums;

enum EmergencyContactTypeEnum: string
{
    case EMERGENCY = 'emergency';
    case SECONDARY = 'secondary';
    case OTHER = 'other';

    /**
     * Get all values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Check if given value is valid enum
     */
    public static function isValid(string $value): bool
    {
        return in_array($value, self::values(), true);
    }
}
