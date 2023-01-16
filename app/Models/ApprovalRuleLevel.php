<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalRuleLevel extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function employee_detail()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id')->withTrashed();
    }

    public function approval_rule()
    {
        return $this->belongsTo(ApprovalRule::class, 'approval_rule_id', 'id');
    }
}
