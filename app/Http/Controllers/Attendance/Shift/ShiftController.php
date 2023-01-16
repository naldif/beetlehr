<?php

namespace App\Http\Controllers\Attendance\Shift;

use App\Actions\Options\GetBranchOptions;
use App\Http\Controllers\AdminBaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\Shift\CreateShiftRequest;
use App\Http\Resources\Attendances\Shift\ShiftResource;
use App\Http\Resources\Attendances\Shift\SubmitShiftResource;
use App\Services\Attendance\Shift\ShiftService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShiftController extends AdminBaseController
{
    public function __construct(ShiftService $shiftService, GetBranchOptions $getBranchOptions)
    {
        $this->shiftService = $shiftService;
        $this->getBranchOptions = $getBranchOptions;
    }
    
    public function getShiftIndex(){
        // Filter Branches
        $branchOptions = [
            'all' => 'All Branches'
        ];
        foreach ($this->getBranchOptions->handle() as $key => $value) {
            $branchOptions[$key] = $value;
        }
        return Inertia::render($this->source . 'attendance/shift/Index', [
            "title" => 'BattleHR | Attendance - Shift',
            "additional" => [
                'branch_list' => $branchOptions
            ]
        ]);
    }

    public function getShiftList(Request $request){
        try {
            $data = $this->shiftService->getData($request);
            $result = new ShiftResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function createShift(CreateShiftRequest $request){
        try {
            $data = $this->shiftService->storeData($request);
            $result = new SubmitShiftResource($data,'Success generate new shift');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function editShift($id,CreateShiftRequest $request){
        try {
            $data = $this->shiftService->updateData($id,$request);
            $result = new SubmitShiftResource($data,'Success update this shift');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function deleteShift($id){
        try {
            $data = $this->shiftService->destroyData($id);
            $result = new SubmitShiftResource($data,'Success delete this shift');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
