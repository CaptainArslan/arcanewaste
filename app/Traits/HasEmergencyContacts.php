<?php

namespace App\Traits;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasEmergencyContacts
{
    public function emergencyContacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable');
    }

    public function emergencyContactsByType($type): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable')->where('type', $type);
    }

    public function createEmergencyContact(array $contact): ?Contact
    {
        return $this->emergencyContacts()->create([
            'name' => $contact['name'],
            'phone' => $contact['phone'],
            'type' => $contact['type'],
        ]);
    }

    public function updateEmergencyContact(array $contact, $id): ?Contact
    {
        return $this->emergencyContacts()->where('id', $id)->update([
            'name' => $contact['name'],
            'phone' => $contact['phone'],
            'type' => $contact['type'],
        ]);
    }

    public function deleteEmergencyContact($id): ?bool
    {
        return $this->emergencyContacts()->where('id', $id)->delete();
    }
}
