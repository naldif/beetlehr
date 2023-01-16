<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BpjskSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $table = 'bpjsk_settings';

    protected $appends = [
        'minimum_value_formatted'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function getMinimumValueFormattedAttribute()
    {
        return number_format($this->minimum_value, 2, ',', '.');
    }
}
