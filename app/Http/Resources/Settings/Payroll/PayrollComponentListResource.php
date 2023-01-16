<?php

namespace App\Http\Resources\Settings\Payroll;

use App\Actions\Utility\Payroll\ComponentMetaFormat;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PayrollComponentListResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
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
                "message" => "Success get payroll component lists",
                'pagination' => $this->metaData()
            ]
        ];
    }

    private function transformData($data)
    {
        $formatMeta = new ComponentMetaFormat();

        if($data->custom_attribute !== null && array_key_exists('action', $data->custom_attribute)){
            $custom_attribute = $formatMeta->handle($data->custom_attribute, $data->custom_attribute['action']);
        }else{
            $custom_attribute = $data->custom_attribute;
        }

        return [
            'id' => $data->id,
            'name' => $data->name,
            'type' => $data->type,
            'is_mandatory' => $data->is_mandatory,
            'is_editable' => $data->is_editable,
            'is_taxable' => $data->is_taxable,
            'custom_attribute' => $custom_attribute
        ];
    }

    private function transformCollection($collection)
    {
        return $collection->transform(function ($data) {
            return $this->transformData($data);
        });
    }

    private function metaData()
    {
        return [
            "total" => $this->total(),
            "count" => $this->count(),
            "per_page" => (int)$this->perPage(),
            "current_page" => $this->currentPage(),
            "total_pages" => $this->lastPage(),
            "links" => [
                "next" => $this->nextPageUrl()
            ],
        ];
    }
}
