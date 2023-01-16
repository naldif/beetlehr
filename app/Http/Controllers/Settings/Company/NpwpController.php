<?php

namespace App\Http\Controllers\Settings\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminBaseController;
use App\Services\Settings\Company\NpwpService;
use App\Http\Requests\Settings\Company\CreateNpwpRequest;
use App\Http\Requests\Settings\Company\UpdateNpwpRequest;
use App\Http\Resources\Settings\Company\NpwpListResource;
use App\Http\Resources\Settings\Company\SubmitNpwpResource;

class NpwpController extends AdminBaseController
{
    public function __construct(NpwpService $npwpService)
    {
        $this->npwpService = $npwpService;
    }

    public function getNpwpList(Request $request)
    {
        try {
            $data = $this->npwpService->getData($request);

            $result = new NpwpListResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function createNpwp(CreateNpwpRequest $request)
    {
        try {
            $data = $this->npwpService->storeNpwp($request);
            $result = new SubmitNpwpResource($data, 'Success Create Npwp');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function updateNpwp($id, UpdateNpwpRequest $request)
    {
        try {
            $data = $this->npwpService->updateNpwp($id, $request);
            $result = new SubmitNpwpResource($data, 'Success Update Npwp');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function deleteNpwp($id)
    {
        try {
            $data = $this->npwpService->deleteNpwp($id);
            $result = new SubmitNpwpResource($data, 'Success Delete Npwp');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
