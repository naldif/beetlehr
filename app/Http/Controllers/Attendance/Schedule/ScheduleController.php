<?php

namespace App\Http\Controllers\Attendance\Schedule;

use App\Actions\Options\GetBranchOptions;
use App\Actions\Options\GetShiftOptions;
use App\Http\Controllers\AdminBaseController;
use App\Http\Requests\Attendance\Schedule\BulkScheduleCreate;
use App\Http\Resources\Attendances\Schedule\GetReportScheduleResource;
use App\Http\Resources\Attendances\Schedule\GetScheduleResource;
use App\Http\Resources\Attendances\Schedule\SubmitScheduleResource;
use App\Models\Shift;
use App\Services\Attendance\Schedule\ScheduleService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ScheduleController extends AdminBaseController
{
    public function __construct(GetBranchOptions $getBranchOptions ,GetShiftOptions $getShiftOptions,ScheduleService $scheduleService)
    {
        $this->getBranchOptions = $getBranchOptions;
        $this->getShiftOptions = $getShiftOptions;
        $this->scheduleService = $scheduleService;
    }

    public function getScheduleIndex()
    {
        // Filter Branches
        $branchOptions = [];
        foreach ($this->getBranchOptions->handle() as $key => $value) {
            $branchOptions[$key] = $value;
        }

        $date = Carbon::now();
        $daysInMonth = $date->daysInMonth;
        $head = ['Employee Name'];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $head[$i] = $i;
        }
        
        $headReport = ['Date'];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $headReport[$i] = $i;
        }

        return Inertia::render($this->source . 'attendance/schedule/Index', [
            "title" => 'BattleHR | Attendance - Schedule',
            "additional" => [
                'branch_list' => $branchOptions,
                'head' => $head,
                'headReport' => $headReport
            ]
        ]);
    }

    public function getSchedule(Request $request){
        try {
            $data = $this->scheduleService->getData($request);
            $result = new GetScheduleResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function getShiftOptions(Request $request)
    {
        try {
            $shifts = Shift::where('branch_id', $request->branch_id)->get()->pluck('name', 'id');
            $choose = [
                'assign' => 'Un Assign',
                'libur' => 'Libur'
            ];
            $shift_list = [
                'libur' => 'Libur'
            ];
            foreach ($shifts as $key => $value) {
                $choose[$key] = $value;
                $shift_list[$key] = $value;
            }
            
            return $this->respond([
                'choose' => $choose,
                'shift_list' => $shift_list
            ]);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function getReport(Request $request){
        try {
            $data = $this->scheduleService->getReport($request);
            $result = new GetReportScheduleResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function getGroupByBranch($branch_id){
        try {
            $data = $this->scheduleService->getGroupByBranch($branch_id);
            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function getEmployeeByBranch($branch_id){
        try {
            $data = $this->scheduleService->getEmployeeByBranch($branch_id);
            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function bulkStore(BulkScheduleCreate $request){
        try {
            $data = $this->scheduleService->storeBulkSchedule($request);
            $result = new SubmitScheduleResource($data,'Success generate schedules');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function updateSchedule($user_id,Request $request){
        try {
            $data = $this->scheduleService->updateSchedule($user_id,$request);
            $result = new SubmitScheduleResource($data,$data['msg'],$data['success']);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
