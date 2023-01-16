<?php

namespace App\Helpers\Utility;


class Time 
{
    public static function calculateTotalHours($array_of_hours)
    {
        if (count($array_of_hours) > 0) {
            $sum = strtotime('00:00:00');
            $hours_total = 0;

            foreach ($array_of_hours as $element) {
                $timeinsec = strtotime($element) - $sum;
                $hours_total = $hours_total + $timeinsec;
            }
            $hours = floor($hours_total / 3600);
            $mins = floor($hours_total / 60 % 60);
            $secs = floor($hours_total % 60);

            $result_total_hours = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
        } else {
            $result_total_hours = "00:00:00";
        }

        return $result_total_hours;
    }

    public static function calculateHours($array_of_hours)
    {
        if (count($array_of_hours) > 0) {
            $sum = strtotime('00:00:00');
            $hours_total = 0;

            foreach ($array_of_hours as $element) {
                $timeinsec = strtotime($element) - $sum;
                $hours_total = $hours_total + $timeinsec;
            }
            $result_total_hours = number_format($hours_total / 3600, 2);
        } else {
            $result_total_hours = 0;
        }

        return $result_total_hours;
    }
}
