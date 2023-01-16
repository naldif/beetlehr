<?php

namespace App\Http\Controllers\Settings\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminBaseController;
use App\Services\Settings\Company\BranchService;
use App\Http\Requests\Settings\Company\CreateBranchRequest;
use App\Http\Requests\Settings\Company\UpdateBranchRequest;
use App\Http\Resources\Settings\Company\BranchListResource;
use App\Http\Resources\Settings\Company\SubmitBranchResource;

class BranchController extends AdminBaseController
{
    public function __construct(BranchService $branchService)
    {
        $this->branchService = $branchService;
    }

    public function getBranchList(Request $request)
    {
        try {
            $data = $this->branchService->getData($request);

            $result = new BranchListResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function createBranch(CreateBranchRequest $request)
    {
        try {
            $data = $this->branchService->storeBranch($request);
            $result = new SubmitBranchResource($data, 'Success Create Branch');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function updateBranch($id, UpdateBranchRequest $request)
    {
        try {
            $data = $this->branchService->updateBranch($id, $request);
            $result = new SubmitBranchResource($data, 'Success Update Branch');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function deleteBranch($id)
    {
        try {
            $data = $this->branchService->deleteBranch($id);
            $result = new SubmitBranchResource($data, 'Success Delete Branch');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
