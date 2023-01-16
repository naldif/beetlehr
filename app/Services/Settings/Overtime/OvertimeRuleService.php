<?php

namespace App\Services\Settings\Overtime;

use App\Models\LeaveType;
use App\Models\OvertimeRule;

class OvertimeRuleService
{
    public function getData($request)
    {
        $search = $request->search;

        // Get company
        $query = OvertimeRule::query();

        // Filter By Params
        $query->when(request('search', false), function ($q) use ($search) {
            $q->where(function($qu) use ($search) {
                $qu->where('clock_in', $search)->orWhere('clock_out', $search);
            });
        });

        return $query->paginate(10);
    }

    public function storeData($request)
    {
        $input = [];
        $input['clock_in'] = $request->clock_in['hours'] . ':' . $request->clock_in['minutes'] . ':00';
        $input['clock_out'] = $request->clock_out['hours'] . ':' . $request->clock_out['minutes'] . ':00';

        $rule = OvertimeRule::create($input);

        return $rule;
    }

    public function updateData($id, $request)
    {
        $input = [];
        $input['clock_in'] = $request->clock_in['hours'] . ':' . $request->clock_in['minutes'] . ':00';
        $input['clock_out'] = $request->clock_out['hours'] . ':' . $request->clock_out['minutes'] . ':00';

        $rule = OvertimeRule::findOrFail($id);
        $rule->update($input);

        return $rule;
    }

    public function deleteData($id)
    {
        $rule = OvertimeRule::findOrFail($id);
        $rule->delete();

        return $rule;
    }
}
