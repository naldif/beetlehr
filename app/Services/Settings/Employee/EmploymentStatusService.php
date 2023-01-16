<?php

namespace App\Services\Settings\Employee;

use App\Models\EmploymentStatus;

class EmploymentStatusService
{
    public function getData($request)
    {
        $search = $request->search;

        // Get employment status
        $query = EmploymentStatus::query();

        // Filter By Params
        $data = $query->when(request('search', false), function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        });

        return $data->paginate(10);
    }

    public function storeEmploymentStatus($request)
    {
        $input = [
            'name' => $request->name,
            'pkwt_type' => strtolower($request->pkwt_type),
            'employment_type' => $request->pkwt_type == 'pkwt' ? 'Pegawai Tidak Tetap' : 'Pegawai Tetap',
            'status' => $request->status ?? 0
        ];

        $employmentStatus = EmploymentStatus::create($input);

        return $employmentStatus;
    }

    public function updateEmploymentStatus($id, $request)
    {
        $input = [
            'name' => $request->name,
            'pkwt_type' => strtolower($request->pkwt_type),
            'employment_type' => $request->pkwt_type == 'pkwt' ? 'Pegawai Tidak Tetap' : 'Pegawai Tetap',
            'status' => $request->status ?? 0
        ];

        $employmentStatus = EmploymentStatus::findOrFail($id);
        $employmentStatus->update($input);

        return $employmentStatus;
    }

    public function deleteEmploymentStatus($id)
    {
        $employmentStatus = EmploymentStatus::findOrFail($id);
        $employmentStatus->delete();

        return $employmentStatus;
    }
}
