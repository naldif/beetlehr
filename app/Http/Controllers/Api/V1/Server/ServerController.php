<?php

namespace App\Http\Controllers\Api\V1\Server;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiBaseController;
use App\Services\Api\V1\Server\ServerService;
use App\Http\Resources\Api\V1\Server\ServerGetStatusResource;

class ServerController extends ApiBaseController
{
    public function __construct(ServerService $service)
    {
        $this->serverService = $service;
    }

    public function getStatus(Request $request)
    {
        try {
            $data = $this->serverService->getStatus($request);

            $return = new ServerGetStatusResource($data, 'Success Get Status Information');
            return $this->respond($return);
        } catch (\Throwable $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
