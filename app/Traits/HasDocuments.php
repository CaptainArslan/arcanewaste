<?php

namespace App\Traits;

use App\Models\Document;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

trait HasDocuments
{
    /**
     * Polymorphic relationship: documents
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    /**
     * Create new documents (ignores duplicates by file_path)
     */
    public function createDocuments(array $documents = []): Collection
    {
        if (empty($documents)) {
            return $this->documents()->get();
        }

        $newDocuments = [];
        $existingDocuments = $this->documents()->pluck('file_path')->toArray();

        foreach ($documents as $document) {
            if (! in_array($document['file_path'], $existingDocuments)) {
                $newDocuments[] = $this->filterDocumentData($document);
            }
        }

        return $this->documents()->createMany($newDocuments);
    }

    /**
     * Update existing documents or create if not exists (by file_path)
     */
    public function updateDocuments(array $documents = []): Collection
    {
        if (empty($documents)) {
            return $this->documents()->get();
        }

        foreach ($documents as $document) {
            $data = $this->filterDocumentData($document);

            $docModel = $this->documents()->where('file_path', $document['file_path'])->first();

            if ($docModel) {
                $docModel->update($data);
            } else {
                $this->documents()->create($data);
            }
        }

        return $this->documents()->get();
    }

    /**
     * Delete specific documents by file_path
     */
    public function deleteDocuments(array $documents = []): Collection
    {
        if (empty($documents)) {
            return $this->documents()->get();
        }

        $filePaths = collect($documents)->pluck('file_path')->toArray();

        $this->documents()->whereIn('file_path', $filePaths)->delete();

        return $this->documents()->get();
    }

    /**
     * Whitelist allowed document fields
     */
    protected function filterDocumentData(array $document): array
    {
        $allowed = [
            'name',
            'type',
            'file_path',
            'mime_type',
            'issued_at',
            'expires_at',
            'is_verified',
        ];

        return array_intersect_key($document, array_flip($allowed));
    }
}
