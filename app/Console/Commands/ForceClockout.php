<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Schedule;
use App\Models\BreakTime;
use App\Models\Attendance;
use Illuminate\Console\Command;
use App\Models\BreakTimeOffline;
use App\Models\AttendanceOffline;
use App\Actions\Utility\Attendance\ForceClockoutAttendance;

class ForceClockout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:force-clockout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to force clockout all attendance between today and yesterday date range';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::now()->format('Y-m-d');
        $yesterday = Carbon::now()->subDays(1)->format('Y-m-d');
        $attendances = Attendance::whereBetween('date_clock', [$yesterday, $today])->where('clock_out', null)->get();
        $attendance_offlines = AttendanceOffline::whereBetween('date_clock', [$yesterday, $today])->where('clock_out', null)->whereNotIn('status', ['cancelled', 'rejected'])->get();
        $schedules = Schedule::whereHas('shift_detail')->whereBetween('date', [$yesterday, $today])->get();
        $breaks = BreakTime::whereIn('attendance_id', $attendances->pluck('id'))->where('end_time', null)->get();
        $break_offlines = BreakTimeOffline::whereIn('attendance_offline_log_id', $attendance_offlines->pluck('id'))->get();
        
        $force_clockout = new ForceClockoutAttendance();
        $force_clockout->handle($attendances, $attendance_offlines, $schedules, $breaks, $break_offlines);

        return Command::SUCCESS;
    }
}
