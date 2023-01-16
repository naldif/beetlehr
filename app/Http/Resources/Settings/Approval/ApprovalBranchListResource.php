<?php

namespace App\Http\Resources\Settings\Approval;

use App\Models\User;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ApprovalBranchListResource extends ResourceCollection
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
                "message" => "Success get approval branch lists",
                'pagination' => $this->metaData()
            ]
        ];
    }

    private function transformData($data)
    {
        if (count($data->approval_rules) > 0) {
            $need_approval_formatted = $data->approval_rules->first()->need_approval ? 'yes' : 'no';
            $need_approval = $data->approval_rules->first()->need_approval;
            $can_delete = true;
            $approval_rule_id = $data->approval_rules->first()->id;
            $approval_rule_levels = $data->approval_rules->first()->approval_rule_levels;
            $employee = $data->approval_rules->first()->approval_rule_levels->map(function ($q) {
                if ($q->employee_id !== null) {
                    $user = User::find($q->employee_detail->user_id);
                    return [
                        'value' => $q->employee_id,
                        'label' => $user->name
                    ];
                } else {
                    return (object)[];
                }
            })->toArray();
        } else {
            $need_approval_formatted = '-';
            $need_approval = null;
            $can_delete = false;
            $approval_rule_id = null;
            $approval_rule_levels = [];
            $employee = [];
        }

        return [
            'id' => $data->id,
            'name' => $data->name,
            'need_approval_formatted' => $need_approval_formatted,
            'need_approval' => $need_approval,
            'can_delete' => $can_delete,
            'approval_rule_id' => $approval_rule_id,
            'approval_rule_levels' => $approval_rule_levels,
            'employee' => $employee,
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
