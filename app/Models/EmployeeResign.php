<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeResign extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function employee_detail()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id')->withTrashed();
    }
}
