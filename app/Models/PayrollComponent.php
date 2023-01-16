<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayrollComponent extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $table = 'payroll_components';

    protected $casts = [
        'custom_attribute' => 'array',
        'is_mandatory' => 'boolean',
        'is_taxable' => 'boolean',
        'is_editable' => 'boolean',
    ];

    public function branch_components()
    {
        return $this->hasMany(PayrollBranchComponent::class, 'component_id', 'id');
    }

    public function employee_components()
    {
        return $this->hasMany(PayrollEmployeeComponent::class, 'component_id', 'id');
    }
}
