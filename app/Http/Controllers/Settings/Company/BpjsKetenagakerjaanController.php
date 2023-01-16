<?php

namespace App\Http\Controllers\Settings\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminBaseController;
use App\Http\Requests\Settings\Company\CreateBpjstkRequest;
use App\Http\Requests\Settings\Company\UpdateBpjstkRequest;
use App\Http\Resources\Settings\Company\BpjstkListResource;
use App\Http\Resources\Settings\Company\SubmitBpjstkResource;
use App\Services\Settings\Company\BpjsKetenagakerjaanService;

class BpjsKetenagakerjaanController extends AdminBaseController
{
    public function __construct(BpjsKetenagakerjaanService $bpjsKetenagakerjaanService)
    {
        $this->bpjsKetenagakerjaanService = $bpjsKetenagakerjaanService;
    }

    public function getBpjstkList(Request $request)
    {
        try {
            $data = $this->bpjsKetenagakerjaanService->getData($request);

            $result = new BpjstkListResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function createBpjstk(CreateBpjstkRequest $request)
    {
        try {
            $data = $this->bpjsKetenagakerjaanService->storeData($request);
            $result = new SubmitBpjstkResource($data, 'Success Create Bpjs Ketenagakerjaan');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function updateBpjstk($id, UpdateBpjstkRequest $request)
    {
        try {
            $data = $this->bpjsKetenagakerjaanService->updateData($id, $request);
            $result = new SubmitBpjstkResource($data, 'Success Update Bpjs Ketenagakerjaan');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function deleteBpjstk($id)
    {
        try {
            $data = $this->bpjsKetenagakerjaanService->deleteData($id);
            $result = new SubmitBpjstkResource($data, 'Success Delete Bpjs Ketenagakerjaan');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
