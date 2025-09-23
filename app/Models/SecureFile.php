<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecureFile extends Model
{

    protected $fillable = [
        'credentials',
        'content_type',
        'code',
    ];

    protected $casts = [
        'credentials' => 'array',
        'content_type' => 'string',
        'code' => 'string',
    ];

    protected $hidden = [
        'credentials',
        'content_type',
        'code',
    ];

    protected $appends = [
        'credentials',
        'content_type',
        'code',
    ];

    // Attribute
    public function getCredentialsAttribute($value)
    {
        return json_decode($value, true);
    }
}
