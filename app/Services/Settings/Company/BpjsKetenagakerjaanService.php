<?php

namespace App\Services\Settings\Company;

use Carbon\Carbon;
use App\Models\BpjstkSetting;

class BpjsKetenagakerjaanService
{
    public function getData($request)
    {
        $search = $request->search;
        $sort_minimum_value = $request->sort_minimum_value;
        $filter_valid_month = $request->filter_valid_month;
        $filter_active_status = $request->filter_active_status === 'true' ? true : false;
        $filter_inactive_status = $request->filter_inactive_status === 'true' ? false : true;

        \Log::debug($request->all());

        // Get company
        $query = BpjstkSetting::whereIn('status', [$filter_active_status, $filter_inactive_status]);

        // Filter By Params
        $query->when(request('search', false), function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        });
        $query->when(request('sort_minimum_value', false), function ($q) use ($sort_minimum_value) {
            $q->orderBy('minimum_value', $sort_minimum_value);
        });
        $query->when(request('filter_valid_month', false), function ($q) use ($filter_valid_month) {
            $month = Carbon::parse($filter_valid_month)->format('m');
            $year = Carbon::parse($filter_valid_month)->format('Y');
            $q->whereMonth('valid_month', $month)->whereYear('valid_month', $year);
        });

        return $query->paginate(10);
    }

    public function storeData($request)
    {
        $input = $request->only(['name', 'registration_number', 'bpjs_office', 'minimum_value', 'valid_month', 'status', 'old_age', 'life_insurance', 'pension_time']);
        $request->bpjstk_risk ? $input['bpjstk_risk_level_id'] = $request->bpjstk_risk_level_id : $input['bpjstk_risk_level_id'] = null;

        $bpjstk = BpjstkSetting::create($input);

        return $bpjstk;
    }

    public function updateData($id, $request)
    {
        $input = $request->only(['name', 'registration_number', 'bpjs_office', 'minimum_value', 'valid_month', 'status', 'old_age', 'life_insurance', 'pension_time']);
        $request->bpjstk_risk ? $input['bpjstk_risk_level_id'] = $request->bpjstk_risk_level_id : $input['bpjstk_risk_level_id'] = null;

        $bpjstk = BpjstkSetting::findOrFail($id);
        $bpjstk->update($input);
        
        return $bpjstk;
    }

    public function deleteData($id)
    {
        $bpjstk = BpjstkSetting::findOrFail($id);
        $bpjstk->delete();

        return $bpjstk;
    }
}
