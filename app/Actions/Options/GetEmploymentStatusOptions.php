<?php

namespace App\Actions\Options;

use App\Models\EmploymentStatus;

class GetEmploymentStatusOptions
{
    public function handle()
    {
        $employmentStatus = EmploymentStatus::where('status', true)->get()->pluck('name', 'id');
        return $employmentStatus;
    }

    public function completeHandle()
    {
        $pkwtType = EmploymentStatus::where('status', true)->get(['id', 'name', 'pkwt_type']);
        return $pkwtType;
    }
}
