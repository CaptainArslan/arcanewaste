<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'contactable_id',
        'contactable_type',
        'name',
        'phone',
        'relation',
        'type',
    ];

    protected $casts = [
        'contactable_id' => 'integer',
        'contactable_type' => 'string',
        'name' => 'string',
        'phone' => 'string',
        'relation' => 'string',
        'type' => 'string',
    ];

    protected $hidden = [
        'contactable_id',
        'contactable_type',
    ];

    // Relationships
    public function contactable(): MorphTo
    {
        return $this->morphTo();
    }
}
