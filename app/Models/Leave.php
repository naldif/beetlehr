<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function employee_detail()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id')->withTrashed();
    }

    public function leave_type_detail()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id', 'id')->withTrashed();
    }
}
