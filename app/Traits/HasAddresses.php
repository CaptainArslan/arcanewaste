<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use App\Models\Address;

trait HasAddresses
{
    public function createAddress(Model $addressable, array $address = [], bool $isPrimary = false): ?Address
    {
        if (empty($address)) {
            return $addressable->addresses()->where('is_primary', true)->first();
        }

        if ($isPrimary) {
            $addressable->addresses()->where('is_primary', true)->update(['is_primary' => false]);
        }

        return $addressable->addresses()->create([
            'address_line1' => $address['address_line1'] ?? null,
            'address_line2' => $address['address_line2'] ?? null,
            'city' => $address['city'] ?? null,
            'state' => $address['state'] ?? null,
            'postal_code' => $address['postal_code'] ?? null,
            'country' => $address['country'] ?? null,
            'latitude' => $address['latitude'] ?? null,
            'longitude' => $address['longitude'] ?? null,
            'is_primary' => (bool) $isPrimary,
        ]);
    }

    public function updateAddress(Model $addressable, array $address, bool $isPrimary = false): ?Address
    {
        $addressModel = $addressable->addresses()->find($address['id'] ?? null);
        if (! $addressModel) return null;

        if ($isPrimary) {
            // Reset other primary addresses
            $addressable->addresses()->where('is_primary', true)->update(['is_primary' => false]);
            $address['is_primary'] = true;
        }

        $addressModel->update(array_merge($address, ['is_primary' => $address['is_primary'] ?? false]));

        return $addressModel;
    }
}
