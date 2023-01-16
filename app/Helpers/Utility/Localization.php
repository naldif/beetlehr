<?php

namespace App\Helpers\Utility;

use Carbon\Carbon;
use GuzzleHttp\Client;

class Localization
{
    public static function convertTimeToUTC($timestamp)
    {
        return Carbon::parse($timestamp)->setTimeZone('UTC');
    }

    public static function convertTimeToUserBranch($timestamp, $timezone)
    {
        return Carbon::parse($timestamp)->setTimeZone($timezone);
    }

    public static function getDataFromLatLong($latitude, $longitude)
    {
        $client = new Client();
        $result = $client->get('https://nominatim.openstreetmap.org/reverse', [
            'query' => [
                'format' => 'jsonv2',
                'lat' => $latitude,
                'lon' => $longitude
            ]
        ]);
        $statusCode = $result->getStatusCode();

        if ($statusCode === 502) {
            throw new \Exception("Maaf sedang ada gangguan. Lokasi akan diupdate nanti", 400);
        } else {
            $resp = $result->getBody()->getContents();
            return json_decode($resp);
        }
    }

    public static function calculateDistanceLocation($latitude, $longitude, $employee)
    {
        // Define Lat Long Branch
        $branch_latitude = $employee->branch_detail->latitude;
        $branch_longitude = $employee->branch_detail->longitude;
        $branch_radius = $employee->branch_detail->radius;

        $theta = floatval($longitude) - floatval($branch_longitude);
        $miles = (sin(deg2rad($latitude))) * sin(deg2rad($branch_latitude)) + (cos(deg2rad($latitude)) * cos(deg2rad($branch_latitude)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $result['miles'] = $miles * 60 * 1.1515;
        $result['kilometers'] = $result['miles'] * 1.609344;
        $result['meters'] = $result['kilometers'] * 1000;
        $result['placement_latitude'] = $branch_latitude;
        $result['placement_longitude'] = $branch_longitude;
        $result['placement_radius'] = $branch_radius;

        return $result;
    }

    public static function checkIsInsideRadius($latitude, $longitude, $branch_latitude, $branch_longitude, $branch_radius)
    {
        $theta = floatval($longitude) - floatval($branch_longitude);
        $miles = (sin(deg2rad($latitude))) * sin(deg2rad($branch_latitude)) + (cos(deg2rad($latitude)) * cos(deg2rad($branch_latitude)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $distance['miles'] = $miles * 60 * 1.1515;
        $distance['kilometers'] = $distance['miles'] * 1.609344;
        $distance['meters'] = $distance['kilometers'] * 1000;

        if ($distance['meters'] <= $branch_radius) {
            $inside_radius = true;
        } else {
            $inside_radius = false;
        }

        return $inside_radius;
    }
}
