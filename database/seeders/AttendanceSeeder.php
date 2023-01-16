<?php

namespace Database\Seeders;

use App\Models\Shift;
use App\Models\Employee;
use App\Models\Schedule;
use Carbon\CarbonPeriod;
use App\Models\Attendance;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');

        $start_date = $this->command->ask('Insert start date range to generated attendance (Y-m-d format)');
        $end_date = $this->command->ask('Insert end date range to generated attendance (Y-m-d format)');
        $employees = $this->command->ask('Insert employee id you want to generate. Separate by comma if you want generate more than one. Attendance wont generated if employee id didnt found');

        // Period
        $period = CarbonPeriod::create($start_date, $end_date);
        $dates = collect($period->toArray())->map(function ($q) {
            return $q->format('Y-m-d');
        });

        // Explode employees
        $employeeInput = explode(',', $employees);

        try {
            DB::beginTransaction();
            foreach ($dates as $key => $date) {
                foreach ($employeeInput as $key => $value) {
                    $employee = Employee::find($value);

                    if ($employee) {
                        // Get Shift by employee branch
                        $shift = Shift::firstOrCreate([
                            'branch_id' => $employee->branch_id,
                        ], [
                            'branch_id' => $employee->branch_id,
                            'name' => 'Normal Shift',
                            'is_night_shift' => false,
                            'start_time' => '08:00:00',
                            'end_time' => '17:00:00',
                        ]);

                        // Get Schedule of this employee branch on this date
                        $schedule = Schedule::firstOrCreate([
                            'user_id' => $employee->user_id,
                            'date' => $date
                        ], [
                            'user_id' => $employee->user_id,
                            'date' => $date,
                            'shift_id' => $shift->id,
                            'is_leave' => false
                        ]);

                        // Create an attendance
                        Attendance::firstOrCreate([
                            'user_id' => $employee->user_id,
                            'date_clock' => $date
                        ],[
                            'user_id' => $employee->user_id,
                            'status' => 'wfa',
                            'clock_in' => '08:00:00',
                            'clock_out' => '17:00:00',
                            'date_clock' => $date,
                            'latitude_clock_in' => '18.8418867',
                            'longitude_clock_in' => '-10.6909217',
                            'latitude_clock_out' => '18.8418867',
                            'longitude_clock_out' => '-10.6909217',
                            'notes_clock_in' => 'From Seeder',
                            'notes_clock_out' => 'From Seeder',
                            'files_clock_in' => [1],
                            'files_clock_out' => [1],
                            'image_id_clock_in' => 1,
                            'image_id_clock_out' => 1,
                            'address_clock_in' => $faker->address,
                            'address_clock_out' => $faker->address,
                            'is_late_clock_in' => false,
                            'is_early_clock_out' => false,
                            'total_work_hours' => '09:00:00',
                            'total_late_clock_in' => '00:00:00',
                            'total_early_clock_out' => '00:00:00',
                        ]);
                    }
                }
            }
            DB::commit();
        } catch (\Exception $th) {
            DB::rollback();
            $this->command->error($th->getMessage());
        }
        
    }
}
