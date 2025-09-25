<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait HasDocuments
{
    /**
     * Create or get documents for any model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $documentable
     * @param  array  $documents
     * @return \Illuminate\Support\Collection
     */
    public function createDocuments(Model $documentable, array $documents = []): Collection
    {
        if (empty($documents)) {
            return $documentable->documents()->get();
        }

        $newDocuments = [];
        $existingDocuments = $documentable->documents()->pluck('file_path')->toArray();

        foreach ($documents as $document) {
            if (! in_array($document['file_path'], $existingDocuments)) {
                $newDocuments[] = [
                    'name' => $document['name'],
                    'type' => $document['type'],
                    'file_path' => $document['file_path'],
                    'mime_type' => $document['mime_type'],
                    'issued_at' => $document['issued_at'] ?? null,
                    'expires_at' => $document['expires_at'] ?? null,
                    'is_verified' => $document['is_verified'] ?? false,
                ];
            }
        }

        return $documentable->documents()->createMany($newDocuments);
    }
}
