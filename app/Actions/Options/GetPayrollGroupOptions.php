<?php

namespace App\Actions\Options;

use App\Models\PayrollGroup;


class GetPayrollGroupOptions
{
    public function handle()
    {
        $payrollGroup = PayrollGroup::get()->pluck('name', 'id');

        return $payrollGroup;
    }
}
