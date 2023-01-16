<?php

namespace App\Http\Controllers\Employees\Resign;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Services\FileService;
use App\Actions\Options\GetBranchOptions;
use App\Http\Controllers\AdminBaseController;
use App\Services\Employee\Resign\ResignManagementService;
use App\Http\Requests\Employee\Resign\CreateResignRequest;
use App\Http\Requests\Employee\Resign\UpdateResignRequest;
use App\Http\Resources\Employees\Resign\ResignManagementListResource;
use App\Http\Resources\Employees\Resign\SubmitResignManagementResource;

class ResignManagementController extends AdminBaseController
{
    public function __construct(
        GetBranchOptions $getBranchOptions,
        ResignManagementService $resignManagementService, FileService $fileService
    ) {
        $this->getBranchOptions = $getBranchOptions;
        $this->resignManagementService = $resignManagementService;
        $this->fileService = $fileService;

        // Filter Branches
        $branchOptions = [
            'all' => 'All Branches'
        ];
        foreach ($this->getBranchOptions->handle() as $key => $value) {
            $branchOptions[$key] = $value;
        }

        // Select Employee
        $employeeOptions = [];
        $employees = Employee::with(['branch_detail', 'user_detail'])->get();
        foreach ($employees as $value) {
            $employeeOptions[$value->id] = $value->user_detail->name.' - '.$value->branch_detail->name;
        }

        $this->title = "BattleHR | Resign Management";
        $this->path = "employees/resign/index";
        $this->data = [
            'branches_filter' => $branchOptions,
            'employee_list' => $employeeOptions
        ];
    }

    public function getData(Request $request)
    {
        try {
            $data = $this->resignManagementService->getData($request);

            $result = new ResignManagementListResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function createResign(CreateResignRequest $request)
    {
        try {
            $data = $this->resignManagementService->createData($request);

            $result = new SubmitResignManagementResource($data, 'Success Create Resign Submission');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function downloadResignFile($id)
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

    public function deleteResign($id)
    {
        try {
            $data = $this->resignManagementService->deleteData($id);

            $result = new SubmitResignManagementResource($data, 'Success Delete Resign submission');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function updateStatus($id, UpdateResignRequest $request)
    {
        try {
            $data = $this->resignManagementService->updateStatus($id, $request);

            $result = new SubmitResignManagementResource($data, 'Success '.ucfirst($request->action).' Resign submission');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
