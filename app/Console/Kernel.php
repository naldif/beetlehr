<?php

namespace App\Console;

use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('attendance:force-clockout')->everyMinute();
        // $schedule->command('reminder:clockin')->everyMinute();

        $settings = Setting::whereIn('key', ['date_reset_leave_quota'])->get(['key', 'value'])->keyBy('key')->transform(function ($setting) {
            return $setting->value;
        })->toArray();
        if($settings['date_reset_leave_quota']) {
            $day = Carbon::parse($settings['date_reset_leave_quota'])->format('d');
            $month = Carbon::parse($settings['date_reset_leave_quota'])->format('m');
            if(config('app.env') === 'local') {
                $schedule->command('leave:reset-quota')->cron('* * ' . $day . ' ' . $month . ' *');
            }else{
                $schedule->command('leave:reset-quota')->cron('1 0 ' . $day . ' ' . $month . ' *');
            }
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
