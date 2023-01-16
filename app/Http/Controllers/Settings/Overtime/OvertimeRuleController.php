<?php

namespace App\Http\Controllers\Settings\Overtime;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminBaseController;
use App\Services\Settings\Overtime\OvertimeRuleService;
use App\Http\Requests\Settings\Overtime\CreateRuleRequest;
use App\Http\Requests\Settings\Overtime\UpdateRuleRequest;
use App\Http\Resources\Settings\Overtime\OvertimeRuleListResource;
use App\Http\Resources\Settings\Overtime\SubmitOvertimeRuleResource;

class OvertimeRuleController extends AdminBaseController
{
    public function __construct(OvertimeRuleService $overtimeRuleService)
    {
        $this->overtimeRuleService = $overtimeRuleService;
    }

    public function getRuleList(Request $request)
    {
        try {
            $data = $this->overtimeRuleService->getData($request);
            $result = new OvertimeRuleListResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function createRule(CreateRuleRequest $request)
    {
        try {
            $data = $this->overtimeRuleService->storeData($request);
            $result = new SubmitOvertimeRuleResource($data, 'Success Create Rule');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function updateRule($id, UpdateRuleRequest $request)
    {
        try {
            $data = $this->overtimeRuleService->updateData($id, $request);
            $result = new SubmitOvertimeRuleResource($data, 'Success Update Rule');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function deleteRule($id)
    {
        try {
            $data = $this->overtimeRuleService->deleteData($id);
            $result = new SubmitOvertimeRuleResource($data, 'Success Delete Rule');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
