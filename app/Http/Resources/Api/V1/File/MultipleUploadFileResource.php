<?php

namespace App\Http\Resources\Api\V1\File;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MultipleUploadFileResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->transformCollection($this->collection),
            'meta' => [
                "success" => true,
                "message" => "Success Upload Multiple Files",
                'pagination' => (object)[]
            ]
        ];
    }

    private function transformData($data)
    {
        return [
            'id' => $data['id'],
            'url' => $data['url'],
            'file_name' => $data['file_name'],
            'extension' => $data['extension'],
            'size' => $data['size']
        ];
    }

    private function transformCollection($collection)
    {
        return $collection->transform(function ($data) {
            return $this->transformData($data);
        });
    }  
}
