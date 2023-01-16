<?php

namespace App\Http\Controllers\Api\V1\Employee;

use App\Http\Controllers\ApiBaseController;
use App\Services\Api\V1\Employee\ResignService;
use App\Http\Requests\Api\V1\Employee\CreateResignRequest;
use App\Http\Resources\Api\V1\Employee\ResignDetailResource;

class ResignController extends ApiBaseController
{
    public function __construct(ResignService $resignService)
    {
        $this->resignService = $resignService;
    }

    public function getResignData()
    {
        try {
            $data = $this->resignService->getData();

            if(!$data) {
                return $this->emptyDataSuccess('Success Get Resign');
            }

            $result = new ResignDetailResource($data, 'Success Get Resign Data');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function createResign(CreateResignRequest $request)
    {
        try {
            $data = $this->resignService->createData($request);

            $result = new ResignDetailResource($data, 'Success Create Resign Data');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function cancelResign($id)
    {
        try {
            $data = $this->resignService->cancelResign($id);

            $result = new ResignDetailResource($data, 'Success Cancel Resign Data');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
