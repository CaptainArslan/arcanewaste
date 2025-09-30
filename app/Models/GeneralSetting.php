<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class GeneralSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'settingable_id',
        'settingable_type',
        'key',
        'value',
        'type',
        'description',
    ];

    protected $casts = [
        'settingable_id' => 'integer',
        'settingable_type' => 'string',
        'key' => 'string',
        'value' => 'string',
        'type' => 'string',
        'description' => 'string',
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
            get: function ($value) {
                // Try to decode JSON, return original if it's not valid JSON
                $decoded = json_decode($value, true);

                return (json_last_error() === JSON_ERROR_NONE) ? $decoded : $value;
            },
            set: function ($value) {
                // If it's an array or object, encode to JSON
                if (is_array($value) || is_object($value)) {
                    return json_encode($value);
                }

                // For int, float, string, bool → just return as is
                return $value;
            }
        );
    }

    // Scopes
    public function scopeFilters(Builder $query, $filters = []): Builder
    {
        return $query->when($filters, function ($query, $filters) {
            return $query
                ->when(isset($filters['key']), fn ($q) => $q->where('key', $filters['key']))
                ->when(isset($filters['id']), fn ($q) => $q->where('id', $filters['id']))
                ->when(isset($filters['value']), fn ($q) => $q->where('value', $filters['value']))
                ->when(isset($filters['type']), fn ($q) => $q->where('type', $filters['type']))
                ->when(isset($filters['description']), fn ($q) => $q->where('description', $filters['description']));
        });
    }

    public function scopeSort(Builder $query, $sort = []): Builder
    {
        return $query->when($sort, function ($query, $sort) {
            return $query->orderBy($sort['column'], $sort['direction']);
        });
    }
}
