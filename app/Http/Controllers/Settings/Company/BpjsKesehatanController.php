<?php

namespace App\Http\Controllers\Settings\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminBaseController;
use App\Services\Settings\Company\BpjsKesehatanService;
use App\Http\Requests\Settings\Company\CreateBpjskRequest;
use App\Http\Requests\Settings\Company\UpdateBpjskRequest;
use App\Http\Resources\Settings\Company\BpjskListResource;
use App\Http\Resources\Settings\Company\SubmitBpjskResource;

class BpjsKesehatanController extends AdminBaseController
{
    public function __construct(BpjsKesehatanService $bpjsKesehatanService)
    {
        $this->bpjsKesehatanService = $bpjsKesehatanService;
    }

    public function getBpjskList(Request $request)
    {
        try {
            $data = $this->bpjsKesehatanService->getData($request);

            $result = new BpjskListResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function createBpjsk(CreateBpjskRequest $request)
    {
        try {
            $data = $this->bpjsKesehatanService->storeData($request);
            $result = new SubmitBpjskResource($data, 'Success Create Bpjs Kesehatan');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function updateBpjsk($id, UpdateBpjskRequest $request)
    {
        try {
            $data = $this->bpjsKesehatanService->updateData($id, $request);
            $result = new SubmitBpjskResource($data, 'Success Update Bpjs Kesehatan');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function deleteBpjsk($id)
    {
        try {
            $data = $this->bpjsKesehatanService->deleteData($id);
            $result = new SubmitBpjskResource($data, 'Success Delete Bpjs Kesehatan');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
