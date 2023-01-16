<?php

namespace App\Actions\Utility;

use Carbon\Carbon;
use App\Models\Setting;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class ReminderClockin
{
    public function __construct()
    {
        // Get setting related to attendance
        $settings = Setting::whereIn('key', ['tolerance_notification'])->get(['key', 'value'])->keyBy('key')
        ->transform(function ($setting) {
            return $setting->value;
        })->toArray();

        $this->settings = $settings;
    }

    public function countDiff($timezone, $today, $type)
    {
        $to = Carbon::createFromFormat('Y-m-d H:i', $today . ' ' . $type);
        $from = Carbon::now();
        $toConverted = Carbon::createFromFormat('Y-m-d H:i:s', $to->format('Y-m-d H:i:s'), $timezone);
        $fromConverted = Carbon::createFromFormat('Y-m-d H:i:s', $from->format('Y-m-d H:i:s'), $timezone);
        $diff = $toConverted->diff($fromConverted);
        $difference = (($diff->days * 24) * 60) + ($diff->h * 60) + ($diff->i);
        ($diff->invert == 1) ? $result = $difference : $result = '-' . $difference;
        return $result;
    }

    public function handle($shift, $today, $deviceTokens)
    {
        $notification_time = $this->settings['tolerance_notification'] ?: 5;
        
        $messaging = app('firebase.messaging');
        $diff = $this->countDiff($shift->branch_detail->timezone, $today, $shift->start_time);

        $title =  'Jangan Lupa Presensi Jam Masuk Ya';
        $body = "Siap siap yuk " . $diff . " menit lagi harus presensi jam masuk";
        $notification = Notification::create($title, $body);
        $message = CloudMessage::new()->withNotification($notification);

        if (count($deviceTokens) > 0) {
            if ($diff == ($notification_time * 3)) {
                $report = $messaging->sendMulticast($message, $deviceTokens);

                if ($report->hasFailures()) {
                    foreach ($report->failures()->getItems() as $failure) {
                        \Log::info($failure->error()->getMessage());
                    }
                } else {
                    \Log::info('Total Success Notif Push : ' . $report->successes()->count());
                }
            } elseif ($diff == ($notification_time * 2)) {
                $report = $messaging->sendMulticast($message, $deviceTokens);
                if ($report->hasFailures()) {
                    foreach ($report->failures()->getItems() as $failure) {
                        \Log::info($failure->error()->getMessage());
                    }
                } else {
                    \Log::info('Total Success Notif Push : ' . $report->successes()->count());
                }
            } elseif ($diff == $notification_time) {
                $report = $messaging->sendMulticast($message, $deviceTokens);
                if ($report->hasFailures()) {
                    foreach ($report->failures()->getItems() as $failure) {
                        \Log::info($failure->error()->getMessage());
                    }
                } else {
                    \Log::info('Total Success Notif Push : ' . $report->successes()->count());
                }
            }
        }
    }
}
