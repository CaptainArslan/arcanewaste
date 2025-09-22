<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'file_path' => $this->file_path,
            'mime_type' => $this->mime_type,
            'issued_at' => $this->issued_at,
            'expires_at' => $this->expires_at,
            'is_verified' => $this->is_verified,
        ];
    }
}
