<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function npwp_list_detail()
    {
        return $this->belongsTo(NpwpList::class, 'npwp_list_id', 'id')->withTrashed();
    }

    public function payroll_branch_components()
    {
        return $this->hasMany(PayrollBranchComponent::class, 'branch_id', 'id');
    }

    public function approval_rules()
    {
        return $this->hasMany(ApprovalRule::class, 'branch_id', 'id');
    }
}
