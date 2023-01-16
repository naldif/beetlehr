<?php

namespace App\Http\Controllers\NoticeBoard;

use Illuminate\Http\Request;
use App\Services\FileService;
use App\Actions\Options\GetBranchOptions;
use App\Http\Controllers\AdminBaseController;
use App\Services\NoticeBoard\NoticeBoardService;
use App\Http\Requests\NoticeBoard\CreateNoticeBoardRequest;
use App\Http\Requests\NoticeBoard\UpdateNoticeBoardRequest;
use App\Http\Resources\NoticeBoard\NoticeBoardListResource;
use App\Http\Resources\NoticeBoard\SubmitNoticeBoardResource;

class NoticeBoardController extends AdminBaseController
{
    public function __construct(
        GetBranchOptions $getBranchOptions,
        NoticeBoardService $noticeBoardService,
        FileService $fileService
    ) {
        $this->getBranchOptions = $getBranchOptions;
        $this->noticeBoardService = $noticeBoardService;
        $this->fileService = $fileService;

        // Select Branch Options
        $branchOptions = [
            'all' => 'All Branches'
        ];
        foreach ($this->getBranchOptions->handle() as $key => $value) {
            $branchOptions[$key] = $value;
        }

        $this->title = "BattleHR | Notice Board";
        $this->path = "noticeboard/index";
        $this->data = [
            'branches_filter' => $branchOptions
        ];
    }

    public function getData(Request $request)
    {
        try {
            $data = $this->noticeBoardService->getData($request);

            $result = new NoticeBoardListResource($data);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function createData(CreateNoticeBoardRequest $request)
    {
        try {
            $data = $this->noticeBoardService->createData($request);

            $result = new SubmitNoticeBoardResource($data, 'Success Create Notice Board');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function updateData($id, UpdateNoticeBoardRequest $request)
    {
        try {
            $data = $this->noticeBoardService->updateData($id, $request);

            $result = new SubmitNoticeBoardResource($data, 'Success Update Notice Board');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function deleteData($id)
    {
        try {
            $data = $this->noticeBoardService->deleteData($id);

            $result = new SubmitNoticeBoardResource($data, 'Success Delete Notice Board');
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
