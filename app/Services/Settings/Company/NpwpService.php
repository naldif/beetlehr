<?php

namespace App\Services\Settings\Company;

use App\Models\NpwpList;

class NpwpService
{
    public function getData($request)
    {
        $search = $request->search;

        // Get company
        $query = NpwpList::query();

        // Filter By Params
        $query->when(request('search', false), function ($q) use ($search) {
            $q->where('npwp_name', 'like', '%' . $search . '%');
        });

        return $query->paginate(10);
    }

    public function storeNpwp($request)
    {
        $input = $request->only(['npwp_name', 'number_npwp', 'npwp_company_name', 'address', 'city', 'postal_code', 'kpp', 'active_month', 'status']);

        $npwp = NpwpList::create($input);

        return $npwp;
    }

    public function updateNpwp($id, $request)
    {
        $input = $request->only(['npwp_name', 'number_npwp', 'npwp_company_name', 'address', 'city', 'postal_code', 'kpp', 'active_month', 'status']);

        $npwp = NpwpList::findOrFail($id);
        $npwp->update($input);
        
        return $npwp;
    }

    public function deleteNpwp($id)
    {
        $npwp = NpwpList::findOrFail($id);
        $npwp->delete();

        return $npwp;
    }
}
