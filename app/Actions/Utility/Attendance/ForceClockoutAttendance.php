<?php

namespace App\Actions\Utility\Attendance;

use Carbon\Carbon;
use App\Models\Setting;
use App\Helpers\Utility\Time;
use App\Helpers\Utility\Attendance\BreakAttendance;

class ForceClockoutAttendance
{
    public function __construct()
    {
        // Get setting related to attendance
        $settings = Setting::whereIn('key', ['time_for_force_clockout_type', 'time_for_force_clockout_fixed', 'time_for_force_clockout_minutes'])->get(['key', 'value'])->keyBy('key')
            ->transform(function ($setting) {
                return $setting->value;
            })->toArray();

        $this->settings = $settings;
    }

    public function handle($attendances, $attendance_offlines, $schedules ,$breaks, $break_offlines)
    {
        if($this->settings['time_for_force_clockout_type'] === 'minutes') {
            $this->minutesClockout($attendances, $attendance_offlines, $schedules, $breaks, $break_offlines);
        }else{
            $this->fixedClockout($attendances, $attendance_offlines, $schedules, $breaks, $break_offlines);
        }
    }

    public function minutesClockout($attendances, $attendance_offlines, $schedules, $breaks, $break_offlines)
    {
        $time_for_force_clockout = $this->settings['time_for_force_clockout_minutes'] ?: 0;
    
        // Force Clockout Scheduled Attendance
        foreach ($attendances as $attendance) {
            $schedule = collect($schedules)->where('date', $attendance->date_clock)->where('user_id', $attendance->user_id)->first();
            if(isset($schedule)) {
                $schedule_end_time = Carbon::createFromFormat('Y-m-d H:i', $schedule->date . ' ' . $schedule->shift_detail->end_time);

                if ($schedule->shift_detail->is_night_shift == 1) {
                    $schedule_time = Carbon::parse($schedule_end_time)->addDays(1);
                    $tolerance_clock_out = Carbon::parse($schedule_end_time)->addDays(1)->addMinutes($time_for_force_clockout);
                } else {
                    $schedule_time =  Carbon::parse($schedule_end_time);
                    $tolerance_clock_out = Carbon::parse($schedule_end_time)->addMinutes($time_for_force_clockout);
                }

                $current_time = Carbon::now();
                if($current_time > $tolerance_clock_out) {
                    // Clock All Breaks and Current Attendance
                    $latest_break = collect($breaks)->where('attendance_id', $attendance->id)->where('end_time', null)->last();
                    if($latest_break) {
                        $total_break_hours = BreakAttendance::calculateTotalWorkHours($latest_break->start_time, $schedule_time->format('H:i:s'));
                        $latest_break->update([
                            'end_time' => $schedule_time->format('H:i:s'),
                            'total_work_hours' => $total_break_hours,
                            'note_end_break' => 'Force Clockout by System.',
                            'files_end_break' => []
                        ]);
                    }

                    // Calculate Work Hours
                    $clock_in = Carbon::createFromFormat('Y-m-d H:i:s', $attendance->date_clock . ' ' . $attendance->clock_in);
                    $break_hours = collect($breaks)->where('attendance_id', $attendance->id)->map(function ($q) {
                        return $q->total_work_hours;
                    })->toArray();
                    $total_attendance_work_hours = gmdate("H:i:s", Carbon::parse($schedule_time)->diffInSeconds($clock_in));
                    $total_break_work_hours = Time::calculateTotalHours($break_hours);
                    $final_attendance_work_hours = gmdate("H:i:s", Carbon::parse($total_attendance_work_hours)->diffInSeconds(Carbon::parse($total_break_work_hours)));

                    $attendance->update([
                        'clock_out' => $tolerance_clock_out->format('H:i:s'),
                        'notes_clock_out' => 'Force Clockout by System.',
                        'is_force_clock_out' => 1,
                        'is_early_clock_out' => 0,
                        'total_work_hours' => $final_attendance_work_hours,
                        'total_early_clock_out' => '00:00:00'
                    ]);
                }
            }
        }

        // Force Clockout Offline Attendance
        foreach ($attendance_offlines as $attendance) {
            $current_time = Carbon::now();
            $tolerance_clock_out = Carbon::createFromFormat('Y-m-d H:i:s', $current_time->format('Y-m-d'). ' 23:59:00');

            if($current_time > $tolerance_clock_out) {
                // Clock All Breaks and Current Attendance
                $latest_break = collect($break_offlines)->where('attendance_offline_log_id', $attendance->id)->where('end_time', null)->last()->first();
                $total_break_hours = BreakAttendance::calculateTotalWorkHours($latest_break->start_time, $tolerance_clock_out->format('H:i:s'));
                $latest_break->update([
                    'end_time' => $tolerance_clock_out->format('H:i:s'),
                    'total_work_hours' => $total_break_hours,
                    'note_end_break' => 'Force Clockout by System.',
                    'files_end_break' => []
                ]);

                // Calculate Work Hours
                $clock_in = Carbon::createFromFormat('Y-m-d H:i:s', $attendance->date_clock . ' ' . $attendance->clock_in);
                $break_hours = collect($break_offlines)->where('attendance_offline_log_id', $attendance->id)->map(function ($q) {
                    return $q->total_work_hours;
                })->toArray();
                $total_attendance_work_hours = gmdate("H:i:s", Carbon::parse($tolerance_clock_out)->diffInSeconds($clock_in));
                $total_break_work_hours = Time::calculateTotalHours($break_hours);
                $final_attendance_work_hours = gmdate("H:i:s", Carbon::parse($total_attendance_work_hours)->diffInSeconds(Carbon::parse($total_break_work_hours)));

                $attendance->update([
                    'clock_out' => $tolerance_clock_out->format('H:i:s'),
                    'notes_clock_out' => 'Force Clockout by System.',
                    'is_force_clock_out' => 1,
                    'is_early_clock_out' => 0,
                    'total_work_hours' => $final_attendance_work_hours,
                    'total_early_clock_out' => '00:00:00'
                ]);
            }
        }
    }

    public function fixedClockout($attendances, $attendance_offlines, $schedules, $breaks, $break_offlines)
    {
        $time_for_force_clockout = $this->settings['time_for_force_clockout_fixed'] ?: '23:59:00';
        $time_for_force_clockout = Carbon::parse($time_for_force_clockout);

        // Force Clockout Scheduled Attendance
        foreach ($attendances as $attendance) {
            $schedule = collect($schedules)->where('date', $attendance->date_clock)->where('user_id', $attendance->user_id)->first();
            if (isset($schedule)) {
                $current_time = Carbon::now();
                $tolerance_clock_out = Carbon::createFromFormat('Y-m-d H:i:s', $attendance->date_clock.' '.$time_for_force_clockout->format('H:i:s'));
                $schedule_end_time = Carbon::createFromFormat('Y-m-d H:i', $schedule->date . ' ' . $schedule->shift_detail->end_time);

                if ($current_time > $tolerance_clock_out) {
                    // Clock All Breaks and Current Attendance
                    $latest_break = collect($breaks)->where('attendance_id', $attendance->id)->where('end_time', null)->last();
                    if ($latest_break) {
                        $total_break_hours = BreakAttendance::calculateTotalWorkHours($latest_break->start_time, $schedule_end_time->format('H:i:s'));
                        $latest_break->update([
                            'end_time' => $schedule_end_time->format('H:i:s'),
                            'total_work_hours' => $total_break_hours,
                            'note_end_break' => 'Force Clockout by System.',
                            'files_end_break' => []
                        ]);
                    }

                    // Calculate Work Hours
                    $clock_in = Carbon::createFromFormat('Y-m-d H:i:s', $attendance->date_clock . ' ' . $attendance->clock_in);
                    $break_hours = collect($breaks)->where('attendance_id', $attendance->id)->map(function ($q) {
                        return $q->total_work_hours;
                    })->toArray();
                    $total_attendance_work_hours = gmdate("H:i:s", Carbon::parse($schedule_end_time)->diffInSeconds($clock_in));
                    $total_break_work_hours = Time::calculateTotalHours($break_hours);
                    $final_attendance_work_hours = gmdate("H:i:s", Carbon::parse($total_attendance_work_hours)->diffInSeconds(Carbon::parse($total_break_work_hours)));

                    $attendance->update([
                        'clock_out' => $schedule_end_time->format('H:i:s'),
                        'notes_clock_out' => 'Force Clockout by System.',
                        'is_force_clock_out' => 1,
                        'is_early_clock_out' => 0,
                        'total_work_hours' => $final_attendance_work_hours,
                        'total_early_clock_out' => '00:00:00'
                    ]);
                }
            }
        }

        // Force Clockout Offline Attendance
        foreach ($attendance_offlines as $attendance) {
            $current_time = Carbon::now();
            $tolerance_clock_out = Carbon::createFromFormat('Y-m-d H:i:s', $attendance->date_clock . ' ' . $time_for_force_clockout);

            if ($current_time > $tolerance_clock_out) {
                // Clock All Breaks and Current Attendance
                $latest_break = collect($break_offlines)->where('attendance_offline_log_id', $attendance->id)->where('end_time', null)->last()->first();
                $total_break_hours = BreakAttendance::calculateTotalWorkHours($latest_break->start_time, $tolerance_clock_out->format('H:i:s'));
                $latest_break->update([
                    'end_time' => $tolerance_clock_out->format('H:i:s'),
                    'total_work_hours' => $total_break_hours,
                    'note_end_break' => 'Force Clockout by System.',
                    'files_end_break' => []
                ]);

                // Calculate Work Hours
                $clock_in = Carbon::createFromFormat('Y-m-d H:i:s', $attendance->date_clock . ' ' . $attendance->clock_in);
                $break_hours = collect($break_offlines)->where('attendance_offline_log_id', $attendance->id)->map(function ($q) {
                    return $q->total_work_hours;
                })->toArray();
                $total_attendance_work_hours = gmdate("H:i:s", Carbon::parse($tolerance_clock_out)->diffInSeconds($clock_in));
                $total_break_work_hours = Time::calculateTotalHours($break_hours);
                $final_attendance_work_hours = gmdate("H:i:s", Carbon::parse($total_attendance_work_hours)->diffInSeconds(Carbon::parse($total_break_work_hours)));

                $attendance->update([
                    'clock_out' => $tolerance_clock_out->format('H:i:s'),
                    'notes_clock_out' => 'Force Clockout by System.',
                    'is_force_clock_out' => 1,
                    'is_early_clock_out' => 0,
                    'total_work_hours' => $final_attendance_work_hours,
                    'total_early_clock_out' => '00:00:00'
                ]);
            }
        }
    }
}
