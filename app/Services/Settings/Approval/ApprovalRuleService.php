<?php

namespace App\Services\Settings\Approval;

use App\Models\Employee;
use App\Models\Branch;
use App\Models\ApprovalRule;
use App\Models\ApprovalType;
use App\Models\ApprovalRuleLevel;

class ApprovalRuleService
{
    public function getTypeData($request)
    {
        $search = $request->search;

        $query = ApprovalType::query();

        // Filter By Params
        $query->when(request('search', false), function ($q) use ($search) {
            $q->where('label', 'like', '%' . $search . '%');
        });

        return $query->paginate(10);
    }

    public function getTypeDetail($id)
    {
        $type = ApprovalType::findOrFail($id);
        return $type;
    }

    public function getApprovalBranches($request)
    {
        $approval_type_id = $request->approval_type_id;
        $search = $request->search;

        $query = Branch::with(['approval_rules' => function ($q) use ($approval_type_id) {
            $q->where('approval_type_id', $approval_type_id);
        }, 'approval_rules.approval_rule_levels']);

        // Filter By Params
        $query->when(request('search', false), function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        });

        return $query->paginate(10);
    }

    public function getApprovalEmployeeRule($request)
    {
        $search = $request->search;

        $query = Employee::whereHas('user', function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        })->with(['user_detail']);

        return $query->get()->pluck('user_detail.name', 'id');
    }

    public function updateApprovalRule($request)
    {
        $result = [];

        foreach ($request->rules as $key => $value) {
            // Validate All Employee and approver level values
            if ($value['approver_type'] === 'designated_person') {
                if ($request->employee === null) {
                    throw new \Exception("Please select employee in all level with designated person type", 400);
                }

                if (!array_key_exists($key, $request->employee)) {
                    throw new \Exception("Please select employee in all level with designated person type", 400);
                }

                if ($request->employee[$key] === null) {
                    throw new \Exception("Please select employee in all level with designated person type", 400);
                }

                if (!array_key_exists('value', $request->employee[$key])) {
                    throw new \Exception("Please select employee in all level with designated person type", 400);
                }
            }

            $data = [
                'approval_rule_id' => $value['approval_rule_id'],
                'approver_type' => $value['approver_type'],
                'level_approval' => $key + 1,
                'employee_id' => $value['approver_type'] === 'designated_person' ? $request->employee[$key]['value'] : null
            ];
            array_push($result, $data);
        }

        $approvalRule = ApprovalRule::updateOrCreate([
            'branch_id' => $request->branch_id,
            'approval_type_id' => $request->approval_type_id,
        ], [
            'branch_id' => $request->branch_id,
            'approval_type_id' => $request->approval_type_id,
            'need_approval' => $request->need_approval ? true : false
        ]);

        // Delete All Approval Rule Before creating a new one
        ApprovalRuleLevel::where('approval_rule_id', $approvalRule->id)->delete();

        foreach ($result as $value) {
            // Create a new rule
            $value['approval_rule_id'] = $approvalRule->id;
            ApprovalRuleLevel::create($value);
        }

        return true;
    }

    public function deleteApprovalRule($id)
    {
        $approvalRule = ApprovalRule::findOrFail($id);
        $approvalRule->delete();

        return $approvalRule;
    }
}
