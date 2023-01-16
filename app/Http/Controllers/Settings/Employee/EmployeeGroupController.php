<?php

namespace App\Http\Controllers\Settings\Employee;

use App\Actions\Options\GetEmployeeOptions;
use App\Http\Controllers\AdminBaseController;
use App\Http\Requests\Settings\Employee\CreateGroup;
use App\Http\Resources\Settings\Employee\GroupListResource;
use App\Http\Resources\Settings\Employee\SubmitGroupResource;
use App\Services\Settings\Employee\GroupService;
use Illuminate\Http\Request;

class EmployeeGroupController extends AdminBaseController
{
    public function __construct(GetEmployeeOptions $getEmployeeOptions, GroupService $groupService)
    {
        $this->getEmployeeOptions = $getEmployeeOptions;
        $this->groupService = $groupService;
    }

    public function getEmployeeByBranch(Request $request)
    {
        try {
            $data = $this->getEmployeeOptions->handle($request->branch_id);
            return $this->respond($data);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function getGroupList(Request $request)
    {
        try {
            $data = $this->groupService->getData($request);
            $result = new GroupListResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function createEmployeeGroup(CreateGroup $request)
    {
        try {

            $data = $this->groupService->storeGroup($request);

            $result = new SubmitGroupResource($data, 'Success Create Employee Group');

            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function updateEmployeeGroup($id, CreateGroup $request)
    {
        try {

            $data = $this->groupService->updateGroup($id, $request);

            $result = new SubmitGroupResource($data, 'Success Update Employee Group');

            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function deleteEmployeeGroup($id)
    {
        try {
            $data = $this->groupService->deleteGroup($id);
            $result = new SubmitGroupResource($data, 'Success Delete Employee Group');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
