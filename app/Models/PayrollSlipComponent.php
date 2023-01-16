<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollSlipComponent extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'payroll_slip_components';

    public function payroll_component_detail()
    {
        return $this->belongsTo(PayrollComponent::class, 'payroll_component_id', 'id')->withTrashed();
    }
}
