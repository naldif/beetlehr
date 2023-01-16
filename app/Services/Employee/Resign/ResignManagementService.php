<?php

namespace App\Services\Employee\Resign;

use App\Services\FileService;
use App\Models\EmployeeResign;

class ResignManagementService 
{
    public function getData($request)
    {
        $search = $request->search;
        $filter_branch = $request->filter_branch;

        // Get company
        $query = EmployeeResign::whereHas('employee')->with(['employee_detail']);

        // Filter By Params
        $query->when(request('search', false), function ($q) use ($search) {
            $q->whereHas('employee', function ($qu) use ($search) {
                $qu->whereHas('user_detail', function ($qe) use ($search) {
                    $qe->where('name', 'like', '%' . $search . '%');
                });
            });
        });
        $query->when(request('filter_branch', false), function ($q) use ($filter_branch) {
            if ($filter_branch !== 'all') {
                $q->whereHas('employee_detail', function ($qu) use ($filter_branch) {
                    $qu->where('branch_id', $filter_branch);
                });
            }
        });

        return $query->paginate(10);
    }  

    public function createData($request)
    {
        // Upload File
        $fileService = new FileService();
        $file = $fileService->uploadFile($request->file('file'));

        $inputs = $request->only(['employee_id', 'date', 'end_contract', 'reason']);
        $inputs['file'] = $file->id;
        $inputs['status'] = 'waiting';

        $resign = EmployeeResign::create($inputs);

        return $resign;
    }

    public function deleteData($id)
    {
        $resign = EmployeeResign::findOrFail($id);
        $resign->delete();

        return $resign;
    }

    public function updateStatus($id, $request)
    {
        if($request->action === 'approve') {
            $this->approve($id);
        }else{
            $this->reject($id, $request);
        }
    }

    public function approve($id)
    {
        $resign = EmployeeResign::find($id);
        $resign->update([
            'status' => 'approved'
        ]);

        $resign->employee_detail->update([
            'end_date' => $resign->end_contract
        ]);
    }

    public function reject($id, $request)
    {
        $resign = EmployeeResign::find($id);
        $resign->update([
            'status' => 'rejected',
            'reject_reason' => $request->reject_reason
        ]);
    }
}
