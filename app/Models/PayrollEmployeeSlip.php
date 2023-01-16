<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollEmployeeSlip extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'payroll_employee_slips';

    public function employee_detail()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function payroll_slip()
    {
        return $this->belongsTo(PayrollSlip::class);
    }

    public function payroll_slip_components()
    {
        return $this->hasMany(PayrollSlipComponent::class, 'payroll_employee_slip_id', 'id');
    }
}
