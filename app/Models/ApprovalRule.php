<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalRule extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'need_approval' => 'boolean'
    ];

    public function approval_rule_levels()
    {
        return $this->hasMany(ApprovalRuleLevel::class, 'approval_rule_id', 'id');
    }

    public function approval_type()
    {
        return $this->belongsTo(ApprovalType::class, 'approval_type_id', 'id');
    }
}
