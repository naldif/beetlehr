<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollBranchComponent extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'payroll_branch_components';

    protected $casts = [
        'status' => 'boolean',
    ];
}
