<?php

namespace App\Helpers\Utility;

use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class Authentication
{
    public static function getUserLoggedIn()
    {
        if (Auth::check()) {
            $user = Auth::user();
        } elseif (Auth::guard('employees')->check()) {
            $user = Auth::guard('employees')->user();
        }else{
            $user = null;
        }
       
        return $user;
    }

    public static function logoutUser()
    {
        if (Auth::check()) {
            $user = Auth::logout();
        } elseif (Auth::guard('employees')->check()){
            $user = Auth::guard('employees')->logout();
        }else{
            $user = null;
        }

        return $user;
    }

    public static function getEmployeeLoggedIn()
    {
        $user = Auth::guard('employees')->user();
        $employee = Employee::with(['branch_detail'])->where('user_id', $user->id)->first();

        if(!isset($employee)) {
            throw new \Exception('Employee Not Found', 400);
        }

        return $employee;
    }
}
