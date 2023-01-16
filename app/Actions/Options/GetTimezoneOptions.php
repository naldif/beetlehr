<?php

namespace App\Actions\Options;

use DateTimeZone;


class GetTimezoneOptions
{
    public function handle()
    {
        $timezones = DateTimeZone::listidentifiers();
        $finals = [];
        foreach ($timezones as $key => $value) {
            $finals[$value] = $value;
        }

        return $finals;
    }
}
