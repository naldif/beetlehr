<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollSlip extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'payroll_slips';

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function branch_detail()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id')->withTrashed();
    }

    public function employee_slips()
    {
        return $this->hasMany(PayrollEmployeeSlip::class, 'payroll_slip_id', 'id');
    }

    public function created_by_detail()
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->withTrashed();
    }
}
