<?php

namespace App\Services\Api\V1\Employee;

use App\Models\Employee;
use App\Services\FileService;
use App\Helpers\Utility\Authentication;
use Propaganistas\LaravelPhone\PhoneNumber;
use Illuminate\Validation\ValidationException;

class ProfileService
{
    public function getData()
    {
        $employee = Authentication::getEmployeeLoggedIn();

        return $employee;
    }

    public function updateData($request)
    {
        $file_service = new FileService();
        $employee = Authentication::getEmployeeLoggedIn();
        
        // Checking if phone number exists 
        $phone_number = PhoneNumber::make($request->phone_number, 'ID')->formatE164();
        $phone_number_exists = Employee::where('phone_number', $phone_number)->where('id', '!=', $employee->id)->exists();
        if ($phone_number_exists) {
            throw ValidationException::withMessages(['phone_number' => 'This phone number already exists']);
        }

        $userInputs = $request->only(['name', 'email']);
        $employeeInputs = $request->only(['address', 'account_number']);
        $employeeInputs['phone_number'] = $phone_number;

        if ($request->hasFile('image')) {
            $file = $file_service->uploadFile($request->file('image'));
            $employeeInputs['image'] =  $file->id;
        }

        $employee->update($employeeInputs);
        $employee->user_detail->update($userInputs);

        return $employee;
    }
}
