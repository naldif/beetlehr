<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Shift;
use App\Models\Attendance;
use Illuminate\Console\Command;
use App\Actions\Utility\ReminderClockin as ReminderClockinAction;

class ReminderClockin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:clockin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to send reminder clockin to each user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::now()->format('Y-m-d');
        $shifts = Shift::whereHas(
            'schedules',
            function ($query) use ($today) {
                $query->where('date', $today)->where('is_leave', 0);
            }
        )->with(['schedules' => function($query) use ($today) {
            $query->where('date', $today)->where('is_leave', 0);
        }, 'branch_detail'])->get();
        $attendances = Attendance::where('date_clock', $today)->get();

        $reminder_clockin = new ReminderClockinAction();
        foreach ($shifts as $shift) {
            $deviceTokens = [];
            foreach ($shift->schedules as $schedule) {
                $attendance = collect($attendances)->where('user_id', $schedule->user_id)->where('date_clock', $schedule->date)->first();
                if (!$attendance && $schedule->user_detail->fcm_token) {
                    array_push($deviceTokens, $schedule->user_detail->fcm_token);
                }
            }
            $reminder_clockin->handle($shift, $today, $deviceTokens);
        }
        return Command::SUCCESS;
    }
}
