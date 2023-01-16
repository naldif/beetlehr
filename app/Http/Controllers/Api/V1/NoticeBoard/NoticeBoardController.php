<?php

namespace App\Http\Controllers\Api\V1\NoticeBoard;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiBaseController;
use App\Services\Api\V1\Noticeboard\NoticeBoardService;
use App\Http\Resources\Api\V1\Noticeboard\NoticeBoardListResource;

class NoticeBoardController extends ApiBaseController
{
    public function __construct(NoticeBoardService $noticeBoardService)
    {
        $this->noticeBoardService = $noticeBoardService;
    }

    public function getNoticeBoard(Request $request)
    {
        try {
            $data = $this->noticeBoardService->getData($request);

            $user = new NoticeBoardListResource($data);
            return $this->respond($user);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
