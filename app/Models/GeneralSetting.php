<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GeneralSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'settingable_id',
        'settingable_type',
        'key',
        'value',
        'type',
    ];

    protected $casts = [
        'settingable_id' => 'integer',
        'settingable_type' => 'string',
        'key' => 'string',
        'value' => 'string',
        'type' => 'string',
    ];

    protected $hidden = [
        'settingable_id',
        'settingable_type',
    ];

    // Relationships
    public function settingable(): MorphTo
    {
        return $this->morphTo();
    }
    
    // Scopes 
    public function value(): Attribute
    {
        return Attribute::make(
            get: fn($value) => json_decode($value, true),
        );
    }

    // Scopes
    public function scopeFilter($query, $filters = [])
    {
        return $query->when($filters, function ($query, $filters) {
            return $query
                ->when($filters['key'] ?? false, fn($q, $key) => $q->where('key', $key))
                ->when($filters['id'] ?? false, fn($q, $id) => $q->where('id', $id))
                ->when($filters['value'] ?? false, fn($q, $value) => $q->where('value', $value))
                ->when($filters['type'] ?? false, fn($q, $type) => $q->where('type', $type));
        });
    }

    public function scopeSort($query, $sort = [])
    {
        return $query->when($sort, function ($query, $sort) {
            return $query->orderBy($sort['column'], $sort['direction']);
        });
    }
}
