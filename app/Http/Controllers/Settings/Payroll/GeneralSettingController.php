<?php

namespace App\Http\Controllers\Settings\Payroll;

use App\Http\Controllers\AdminBaseController;
use App\Services\Settings\Payroll\GeneralSettingService;
use App\Http\Requests\Settings\Payroll\UpdateGeneralSettingRequest;
use App\Http\Resources\Settings\Payroll\SubmitGeneralSettingRequest;

class GeneralSettingController extends AdminBaseController
{
    public function __construct(GeneralSettingService $generalSettingService)
    {
        $this->generalSettingService = $generalSettingService;
    }

    public function updateGeneral(UpdateGeneralSettingRequest $request)
    {
        try {
            $data = $this->generalSettingService->updateData($request);

            $result = new SubmitGeneralSettingRequest($data, 'Success Change General Payroll');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
