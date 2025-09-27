<?php

namespace App\Enums;

enum DumpsterStatusEnum: string
{
    case Available = 'available';
    case Rented = 'rented';
    case Maintenance = 'maintenance';
    case Inactive = 'inactive';

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
