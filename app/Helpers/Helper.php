<?php

use Illuminate\Support\Str;

if (! function_exists('normalizeS3Key')) {
    function normalizeS3Key(string $pathOrUrl): ?string
    {
        $bucket = config('filesystems.disks.s3.bucket');
        $key = $pathOrUrl;

        if (filter_var($pathOrUrl, FILTER_VALIDATE_URL)) {
            $parsed = parse_url($pathOrUrl, PHP_URL_PATH) ?: '';
            $key = ltrim(urldecode($parsed), '/');

            if ($bucket && Str::startsWith($key, $bucket . '/')) {
                $key = Str::after($key, $bucket . '/');
            }
        } else {
            $key = ltrim($pathOrUrl, '/');
        }

        return $key !== '' ? $key : null;
    }
}

if (!function_exists('toBoolean')) {
    function toBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (bool) $value;
        }

        $value = strtolower(trim((string) $value));

        return in_array($value, ['1', 'true', 'yes', 'on'], true);
    }
}

if (!function_exists('getPaginated')) {
    function getPaginated($perpage = null)
    {
        return $perpage !== null ? (int) $perpage : 25;
    }
}
