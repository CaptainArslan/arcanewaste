<?php

namespace App\Models;

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

    public function settingable() : MorphTo
    {
        return $this->morphTo();
    }
}
