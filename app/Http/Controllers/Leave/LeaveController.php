<?php

namespace App\Http\Controllers\Leave;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Services\FileService;
use App\Actions\Options\GetBranchOptions;
use App\Http\Controllers\AdminBaseController;
use App\Services\Leave\LeaveManagementService;
use App\Http\Requests\Leave\CreateLeaveRequest;
use App\Http\Requests\Leave\UpdateLeaveRequest;
use App\Http\Resources\Leave\SubmitLeaveResource;
use App\Http\Resources\Leave\LeaveManagementListResource;

class LeaveController extends AdminBaseController
{
    public function __construct(
        GetBranchOptions $getBranchOptions,
        LeaveManagementService $leaveManagementService,
        FileService $fileService
    ) {
        $this->getBranchOptions = $getBranchOptions;
        $this->leaveManagementService = $leaveManagementService;
        $this->fileService = $fileService;

        // Select Employee
        $employeeOptions = [];
        $employees = Employee::with(['branch_detail', 'user_detail'])->get();
        foreach ($employees as $value) {
            $employeeOptions[$value->id] = $value->user_detail->name . ' - ' . $value->branch_detail->name;
        }

        $this->title = "BattleHR | Leave Management";
        $this->path = "leave/index";
        $this->data = [
            'branches_filter' => $this->getBranchOptions->handle(),
            'employee_list' => $employeeOptions
        ];
    }

    public function getData(Request $request)
    {
        try {
            $data = $this->leaveManagementService->getData($request);

            $result = new LeaveManagementListResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function createData(CreateLeaveRequest $request)
    {
        try {
            $data = $this->leaveManagementService->createData($request);

            $result = new SubmitLeaveResource($data, 'Success Create Leave Submission');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function getLeaveType(Request $request)
    {
        try {
            $data = $this->leaveManagementService->getLeaveTypeOptions($request);
            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function donwloadLeaveFile($id)
    {
        try {
            $file = $this->fileService->getFileById($id);
            $tempImage = tempnam(sys_get_temp_dir(), $file->file_name);
            copy($file->full_path, $tempImage);

            return response()->download($tempImage, $file->file_name)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function updateStatus($id, UpdateLeaveRequest $request)
    {
        try {
            $data = $this->leaveManagementService->updateStatus($id, $request);

            $result = new SubmitLeaveResource($data, 'Success ' . ucfirst($request->action) . ' Leave Submission');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function deleteData($id)
    {
        try {
            $data = $this->leaveManagementService->deleteData($id);

            $result = new SubmitLeaveResource($data, 'Success Delete Leave Submission');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
