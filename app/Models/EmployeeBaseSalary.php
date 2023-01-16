<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeBaseSalary extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $table = 'payroll_employee_base_salaries';

    protected $appends = [
        'amount_formatted'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function employee_detail()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id')->withTrashed();
    }

    public function getAmountFormattedAttribute()
    {
        return number_format($this->amount, 2, ',', '.');
    }
}
