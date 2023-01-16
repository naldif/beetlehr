<?php

namespace App\Services\Employee\Employee;

use App\Models\Role;
use App\Models\User;
use App\Models\Setting;
use App\Models\Employee;
use App\Services\FileService;
use Illuminate\Support\Facades\Hash;
use App\Actions\Utility\PaginateCollection;
use App\Actions\Utility\Employee\CalculateEmployeeExternalId;

class EmployeeService
{
    public function getData($request)
    {
        $search = $request->search;
        $filter_branch = $request->filter_branch;
        $filter_designation = $request->filter_designation;
        $filter_role = $request->filter_role;
        $sort_by_name = $request->sort_by_name;

        // Get company
        $query = Employee::whereHas('branch')->whereHas('user')->whereHas('designation')->with(['branch_detail', 'user_detail', 'designation_detail']);

        // Filter By Params
        $query->when(request('search', false), function ($q) use ($search) {
            $q->whereHas('user_detail', function ($qu) use ($search) {
                $qu->where('name', 'like', '%' . $search . '%');
            });
        });
        $query->when(request('filter_branch', false), function ($q) use ($filter_branch) {
            if($filter_branch !== 'all'){
                $q->where('branch_id', $filter_branch);
            }
        });
        $query->when(request('filter_designation', false), function ($q) use ($filter_designation) {
            $q->where('designation_id', $filter_designation);
        });
        $query->when(request('filter_role', false), function ($q) use ($filter_role) {
            $q->whereHas('user_detail', function ($qu) use ($filter_role) {
                $qu->whereHas("roles", function ($qe) use ($filter_role) {
                    $qe->where("id", $filter_role);
                });
            });
        });
        
        // Sort Relation
        if($sort_by_name){      
            if($sort_by_name === 'asc'){
                $result = $query->get()->sortBy('user_detail.name');
            }else{
                $result = $query->get()->sortByDesc('user_detail.name');
            }
            $paginate = new PaginateCollection();
            $result = $paginate->handle($result, 10);
        }else{
            $result = $query->orderBy('id', 'desc')->paginate(10);
        }

        return $result;
    }

    public function getEmployeeSettingRelated()
    {
        $settings = Setting::whereIn('key', ['payroll_istaxable', 'editable_employee_external_id'])->get(['key', 'value'])->keyBy('key')
            ->transform(function ($setting) {
                return $setting->value;
            })->toArray();
            
        return $settings;
    }

    public function getDetailEmployee($id)
    {
        $employee = Employee::with(['branch_detail', 'user_detail', 'designation_detail', 'employment_status_detail', 'ptkp_status_detail', 'payroll_group_detail', 'manager.user_detail'])->findOrFail($id);
        return $employee;
    }

    public function storeData($request)
    {
        // Create User
        $userInput = $request->only(['email']);
        $userInput['user_device'] = !$request->user_device || $request->user_device === 'null'  ? null : $request->user_device;
        $userInput['name'] = $request->employee_name;
        $userInput['password'] = Hash::make($request->password);   
        $user = User::create($userInput);
        $role = Role::findOrFail($request->role_id);
        $user->assignRole($role->name);

        // Upload File
        $fileService = new FileService();
        $file = $fileService->uploadFile($request->file('picture'));

        // Create Employee
        $employeeInput = $request->only(['branch_id', 'designation_id', 'phone_number', 'start_date', 'address', 'account_number', 'bank_name', 'account_name', 'employment_status_id', 'bpjsk_number_card', 'bpjsk_setting_id', 'bpjstk_number_card', 'bpjstk_setting_id']);
        $employeeInput['image'] = $file->id;
        $employeeInput['user_id'] = $user->id;

        $employeeInput['is_use_bpjsk'] =  filter_var($request->is_use_bpjsk, FILTER_VALIDATE_BOOLEAN);
        $employeeInput['is_use_bpjstk'] =  filter_var($request->is_use_bpjstk, FILTER_VALIDATE_BOOLEAN);
        $employeeInput['bpjstk_old_age'] = filter_var($request->bpjstk_old_age, FILTER_VALIDATE_BOOLEAN);
        $employeeInput['bpjstk_life_insurance'] = filter_var($request->bpjstk_life_insurance, FILTER_VALIDATE_BOOLEAN);
        $employeeInput['bpjstk_pension_time'] = filter_var($request->bpjstk_pension_time, FILTER_VALIDATE_BOOLEAN);
        $employeeInput['bpjsk_specific_amount'] = filter_var($request->bpjsk_use_specific_amount, FILTER_VALIDATE_BOOLEAN) ? $request->bpjsk_specific_amount : 0;
        $employeeInput['bpjstk_specific_amount'] = filter_var($request->bpjstk_use_specific_amount, FILTER_VALIDATE_BOOLEAN) ? $request->bpjstk_specific_amount : 0;

        $employeeInput['ptkp_tax_list_id'] = !$request->ptkp_tax_list_id || $request->ptkp_tax_list_id === 'null' ? null : $request->ptkp_tax_list_id;
        $employeeInput['end_date'] = !$request->end_date || $request->end_date === 'null' ? null : $request->end_date;
        $employeeInput['payroll_group_id'] = !$request->payroll_group_id || $request->payroll_group_id === 'null' ? null : $request->payroll_group_id;
        $employeeInput['manager_id'] = !$request->manager_id || $request->manager_id === 'null' ? null : $request->manager_id;
        
        // Calculate Employee External Id
        $generateExternalId = new CalculateEmployeeExternalId();
        $employeeOrderId = $generateExternalId->calculateIncrementalEmployeeId();
        if($request->employee_external_id && $request->employee_external_id !== 'null') {
            $employeeInput['employee_external_id'] = $request->employee_external_id;
            $employeeInput['employee_input_order'] = $employeeOrderId;
        }else{
            $employeeInput['employee_external_id'] = $generateExternalId->handle($request->start_date);
            $employeeInput['employee_input_order'] = $employeeOrderId;
        }

        $employee = Employee::create($employeeInput);

        return $employee;
    }

    public function updateData($id, $request)
    {
        // Update User
        $userInput = $request->only(['email']);
        $userInput['user_device'] = !$request->user_device || $request->user_device === 'null'  ? null : $request->user_device;
        $userInput['name'] = $request->employee_name;
        $userInput['password'] = Hash::make($request->password);
        $user = User::findOrFail($request->user_id);
        $role = Role::findOrFail($request->role_id);
        $user->update($userInput);
        $user->syncRoles($role->name);

        // Upload File if new file exists
        if($request->file('picture')){
            $fileService = new FileService();
            $file = $fileService->uploadFile($request->file('picture'))->id;
        }else{
            $file = $request->image;
        }

        // Create Employee
        $employeeInput = $request->only(['branch_id', 'designation_id', 'phone_number', 'start_date', 'address', 'account_number', 'bank_name', 'account_name', 'employment_status_id', 'bpjsk_number_card', 'bpjsk_setting_id', 'bpjstk_number_card', 'bpjstk_setting_id']);
        $employeeInput['image'] = $file;

        $employeeInput['is_use_bpjsk'] =  filter_var($request->is_use_bpjsk, FILTER_VALIDATE_BOOLEAN);
        $employeeInput['is_use_bpjstk'] =  filter_var($request->is_use_bpjstk, FILTER_VALIDATE_BOOLEAN);
        $employeeInput['bpjstk_old_age'] = filter_var($request->bpjstk_old_age, FILTER_VALIDATE_BOOLEAN);
        $employeeInput['bpjstk_life_insurance'] = filter_var($request->bpjstk_life_insurance, FILTER_VALIDATE_BOOLEAN);
        $employeeInput['bpjstk_pension_time'] = filter_var($request->bpjstk_pension_time, FILTER_VALIDATE_BOOLEAN);
        $employeeInput['bpjsk_specific_amount'] = filter_var($request->bpjsk_use_specific_amount, FILTER_VALIDATE_BOOLEAN) ? $request->bpjsk_specific_amount : 0;
        $employeeInput['bpjstk_specific_amount'] = filter_var($request->bpjstk_use_specific_amount, FILTER_VALIDATE_BOOLEAN) ? $request->bpjstk_specific_amount : 0;

        $employeeInput['ptkp_tax_list_id'] = !$request->ptkp_tax_list_id || $request->ptkp_tax_list_id === 'null' ? null : $request->ptkp_tax_list_id;
        $employeeInput['end_date'] = !$request->end_date || $request->end_date === 'null' ? null : $request->end_date;
        $employeeInput['payroll_group_id'] = !$request->payroll_group_id || $request->payroll_group_id === 'null' ? null : $request->payroll_group_id;
        $employeeInput['manager_id'] = !$request->manager_id || $request->manager_id === 'null' ? null : $request->manager_id;

        // Calculate Employee External Id
        $employee = Employee::findOrFail($id);
        $generateExternalId = new CalculateEmployeeExternalId();
        if ($employee->employee_input_order) {
            $employeeOrderId = $employee->employee_input_order;
        } else {
            $employeeOrderId = $generateExternalId->calculateIncrementalEmployeeId();
        }

        if ($request->employee_external_id && $request->employee_external_id !== 'null') {
            $employeeInput['employee_external_id'] = $request->employee_external_id;
            $employeeInput['employee_input_order'] = $employeeOrderId;
        } else {
            $employeeInput['employee_external_id'] = $generateExternalId->handle($request->start_date, $employeeOrderId);
            $employeeInput['employee_input_order'] = $employeeOrderId;
        }

        $employee->update($employeeInput);

        return $employee;
    }

    public function deleteData($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return $employee;
    }
}
