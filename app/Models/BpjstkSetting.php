<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BpjstkSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $table = 'bpjstk_settings';

    protected $appends = [
        'minimum_value_formatted'
    ];

    protected $casts = [
        'status' => 'boolean',
        'old_age' => 'boolean',
        'life_insurance' => 'boolean',
        'pension_time' => 'boolean',
    ];

    public function getMinimumValueFormattedAttribute()
    {
        return number_format($this->minimum_value, 2, ',', '.');
    }

    public function bpjstk_risk_level()
    {
        return $this->belongsTo(BpjstkRiskLevel::class);
    }
}
