<?php

namespace App\Http\Controllers\Settings\Employee;

use App\Http\Controllers\AdminBaseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Settings\Employee\SubmitGeneralEmployeeResource;
use App\Services\Settings\Employee\GeneralEmployeeService;
use Illuminate\Http\Request;

class EmployeeGeneralController extends AdminBaseController
{
    public function __construct(GeneralEmployeeService $generalEmployeeService)
    {
        $this->generalEmployeeService = $generalEmployeeService;
    }
    
    public function updateEditableNip(Request $request)
    {
        try {
            $data = $this->generalEmployeeService->updateEditableNip($request);

            $result = new SubmitGeneralEmployeeResource($data, 'Success Change Editable Employee External Id Settings');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
