<?php

namespace App\Enums;

enum EmergencyContactTypeEnum: string
{
    case EMERGENCY = 'emergency';
    case SECONDARY = 'secondary';
    case OTHER = 'other';
    case FAMILY = 'family';
    case EXTENDED_FAMILY = 'extended_family';
    case FRIEND = 'friend';
    case WORK = 'work';
    case MEDICAL = 'medical';

    public static function all(): array
    {
        return [
            self::EMERGENCY,
            self::SECONDARY,
            self::FAMILY,
            self::EXTENDED_FAMILY,
            self::FRIEND,
            self::WORK,
            self::MEDICAL,
            self::OTHER,
        ];
    }

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
