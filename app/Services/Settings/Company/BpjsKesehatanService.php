<?php

namespace App\Services\Settings\Company;

use App\Models\BpjskSetting;

class BpjsKesehatanService
{
    public function getData($request)
    {
        $search = $request->search;

        // Get company
        $query = BpjskSetting::query();

        // Filter By Params
        $query->when(request('search', false), function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        });

        return $query->paginate(10);
    }

    public function storeData($request)
    {
        $input = $request->only(['name', 'registration_number', 'bpjs_office', 'minimum_value', 'valid_month', 'status']);

        $bpjsk = BpjskSetting::create($input);

        return $bpjsk;
    }

    public function updateData($id, $request)
    {
        $input = $request->only(['name', 'registration_number', 'bpjs_office', 'minimum_value', 'valid_month', 'status']);

        $bpjsk = BpjskSetting::findOrFail($id);
        $bpjsk->update($input);
        
        return $bpjsk;
    }

    public function deleteData($id)
    {
        $bpjsk = BpjskSetting::findOrFail($id);
        $bpjsk->delete();

        return $bpjsk;
    }
}
