<?php

namespace App\Actions\Utility\Leave;

use Carbon\CarbonPeriod;


class GenerateLeavePeriod
{
    public function handle($leaves)
    {
        $leavePeriod = [];
        foreach ($leaves as $leave) {
            $leavePeriods =  CarbonPeriod::create($leave->start_date, $leave->end_date);
            foreach ($leavePeriods as $date) {
                array_push($leavePeriod, $date->format('Y-m-d'));
            }
        }

        return $leavePeriod;
    }
}
