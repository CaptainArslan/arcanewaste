<?php

namespace App\Traits;

use App\Models\Address;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasAddresses
{
    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function defaultAddress(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable')->where('is_primary', true);
    }

    public function createAddress(array $address = [], bool $isPrimary = false): ?Address
    {
        if (empty($address)) {
            return $this->addresses()->where('is_primary', true)->first();
        }

        if ($isPrimary) {
            $this->addresses()->where('is_primary', true)->update(['is_primary' => false]);
        }

        return $this->addresses()->create([
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

    public function updateAddress(array $address, bool $isPrimary = false): ?Address
    {
        $addressModel = $this->addresses()->find($address['id'] ?? null);
        if (! $addressModel) {
            return null;
        }

        if ($isPrimary) {
            // Reset other primary addresses
            $this->addresses()->where('is_primary', true)->update(['is_primary' => false]);
            $address['is_primary'] = true;
        }

        $addressModel->update(array_merge($address, ['is_primary' => $address['is_primary'] ?? false]));

        return $addressModel;
    }
}
