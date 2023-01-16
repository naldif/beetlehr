<?php

namespace App\Http\Controllers\Api\V1\File;

use Illuminate\Http\Request;
use App\Services\FileService;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\V1\File\MultipleFileRequest;
use App\Http\Requests\Api\V1\File\MultipleOptionalFileRequest;
use App\Http\Resources\Api\V1\File\MultipleUploadFileResource;

class FileController extends ApiBaseController
{
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function uploadOptionalMultipleFile(MultipleOptionalFileRequest $request)
    {
        try {
            $files = [];
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $result = $this->fileService->uploadFile($file);
                    array_push($files, $result);
                }
            }

            $result = new MultipleUploadFileResource($files);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function uploadMultipleFile(MultipleFileRequest $request)
    {
        try {
            $files = [];
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $result = $this->fileService->uploadFile($file);
                    array_push($files, $result);
                }
            }

            $result = new MultipleUploadFileResource($files);
            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
