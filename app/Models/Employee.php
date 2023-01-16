<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'is_use_bpjstk' => 'boolean',
        'is_use_bpjsk' => 'boolean',
        'bpjstk_old_age' => 'boolean',
        'bpjstk_life_insurance' => 'boolean',
        'bpjstk_pension_time' => 'boolean',
    ];

    protected $guarded = ['id'];
    protected $appends = [
        'bpjsk_specific_amount_integer',
        'bpjstk_specific_amount_integer'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function branch_detail()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id')->withTrashed();
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function designation_detail()
    {
        return $this->belongsTo(Designation::class, 'designation_id', 'id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function user_detail()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();;
    }

    public function employment_status_detail()
    {
        return $this->belongsTo(EmploymentStatus::class, 'employment_status_id', 'id')->withTrashed();;
    }

    public function ptkp_status_detail()
    {
        return $this->belongsTo(PtkpTaxList::class, 'ptkp_tax_list_id', 'id');
    }

    public function payroll_group_detail()
    {
        return $this->belongsTo(PayrollGroup::class, 'payroll_group_id', 'id')->withTrashed();
    }

    public function payroll_employee_components()
    {
        return $this->hasMany(PayrollEmployeeComponent::class, 'employee_id', 'id');
    }

    public function getBpjskSpecificAmountIntegerAttribute()
    {
        return (int)$this->bpjsk_specific_amount;
    }

    public function getBpjstkSpecificAmountIntegerAttribute()
    {
        return (int)$this->bpjstk_specific_amount;
    }

    public function payroll_employee_base_salary()
    {
        return $this->hasOne(EmployeeBaseSalary::class, 'employee_id', 'id');
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id', 'id');
    }
}
