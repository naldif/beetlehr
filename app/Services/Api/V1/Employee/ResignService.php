<?php

namespace App\Services\Api\V1\Employee;

use Carbon\Carbon;
use App\Services\FileService;
use App\Models\EmployeeResign;
use App\Helpers\Utility\Authentication;

class ResignService
{
    public function getData()
    {
        // Required Init Data
        $employee = Authentication::getEmployeeLoggedIn();
        $resign = EmployeeResign::where('employee_id', $employee->id)->whereIn('status', ['waiting', 'approved'])->first();

        return $resign;
    }

    public function createData($request)
    {
        $employee = Authentication::getEmployeeLoggedIn();
        $check = EmployeeResign::where('employee_id', $employee->id)->where('status', 'waiting')->exists();
        if ($check) {
            throw new \Exception("Already have resign", 400);
        }

        $inputs = $request->only(['reason', 'is_according_procedure']);
        $inputs['date'] = Carbon::parse($request->date)->format('Y-m-d');
        $inputs['end_contract'] = Carbon::parse($request->end_contract)->format('Y-m-d');
        $inputs['status'] = 'waiting';
        $inputs['employee_id'] = $employee->id;

        // Upload File
        $file_service = new FileService();
        $file = $file_service->uploadFile($request->file('file'));
        $inputs['file'] = $file->id;

        $resign = EmployeeResign::create($inputs);

        return $resign;
    }

    public function cancelResign($id)
    {
        $resign = EmployeeResign::findOrFail($id);

        if ($resign->status !== 'waiting') {
            throw new \Exception("Your resign has been approved or rejected. Cant be cancelled", 400);
        }

        $resign->update([
            'status' => 'cancelled'
        ]);

        return $resign;
    }
}
