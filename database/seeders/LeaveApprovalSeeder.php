<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Employee;
use Carbon\CarbonPeriod;
use App\Models\LeaveType;
use App\Models\LeaveQuota;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Events\Leave\LeaveWasCreated;

class LeaveApprovalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employees = $this->command->ask('Insert employee id you want to generate. Separate by comma if you want generate more than one. Attendance wont generated if employee id didnt found');

        // Explode employees
        $employeeInput = explode(',', $employees);

        try {
            DB::beginTransaction();
            foreach ($employeeInput as $key => $value) {
                $employee = Employee::findOrFail($value);

                $start_date = Carbon::now()->format('Y-m-d');
                $random_day = rand(1, 5);
                $end_date = Carbon::now()->addDays($random_day)->format('Y-m-d');

                $period = CarbonPeriod::create($start_date, $end_date);
                $totalDay = $period->toArray();

                // FirstOrCreate leave type
                $leave_type = LeaveType::firstOrCreate([
                    'branch_id' => $employee->branch_id
                ], [
                    'name' => 'Sakit',
                    'branch_id' => $employee->branch_id,
                    'quota' => 100
                ]);

                $quota = LeaveQuota::where('employee_id', $value)->where('leave_type_id', $leave_type->id)->first();
                if ($quota === null || (isset($quota) && $quota->quota === 0)) {
                    $this->command->error("You did not set quota yet or employee quota now are 0. Please check again");
                } else if ($quota->quota - count($totalDay) < 0) {
                    $this->command->error("The remaining quota is not enough. Update quota first");
                }

                $leaveExists = Leave::where('start_date', '<=', $start_date)->where('end_date', '>=', $end_date)->where('employee_id', $value)->exists();
                if ($leaveExists) {
                    $this->command->error("Employee already have submission in this date range");
                }

                $inputs['employee_id'] = $value;
                $inputs['leave_type_id'] = $leave_type->id;
                $inputs['reason'] = 'Created From Seeder';
                $inputs['start_date'] = $start_date;
                $inputs['end_date'] = $end_date;
                $inputs['status'] = 'waiting';
                $inputs['file'] = 1;

                $leave = Leave::create($inputs);

                // Call Leave Created Event
                event(new LeaveWasCreated($leave, $employee, count($totalDay)));
            }
            DB::commit();
        } catch (\Exception $th) {
            DB::rollBack();
            $this->command->error($th->getMessage(). ' - '. $th->getFile(). ' - '.$th->getLine());
        }
    }
}
