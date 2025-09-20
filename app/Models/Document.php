<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Document extends Model
{
    protected $fillable = [
        'documentable_id',
        'documentable_type',
        'name',
        'type',
        'file_path',
        'mime_type',
        'issued_at',
        'expires_at',
        'is_verified',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'expires_at' => 'date',
        'is_verified' => 'boolean',
    ];

    protected $hidden = [
        'documentable_id',
        'documentable_type',
    ];

    // Relationships    
    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }
}
