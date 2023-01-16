<?php

namespace App\Http\Controllers\Settings\WorkReport;

use App\Http\Controllers\AdminBaseController;
use App\Http\Resources\Settings\WorkReport\SubmitGeneralWorkReportResource;
use App\Services\Settings\WorkReport\WorkReportGeneralService;
use Illuminate\Http\Request;

class WorkReportGeneralController extends AdminBaseController
{
    public function __construct(
        WorkReportGeneralService $workReportGeneralService
    )
    {
        $this->workReportGeneralService = $workReportGeneralService;   
    }

    public function updateMaxTime(Request $request)
    {   
        try {
            $data = $this->workReportGeneralService->updateMaxTime($request);

            $result = new SubmitGeneralWorkReportResource($data, 'Success Change Max Time Work Report');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

}
