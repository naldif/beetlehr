<?php

namespace App\Services\Api\V1\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Setting;
use App\Models\Employee;
use App\Services\FileService;

class AuthenticationService
{
    public function __construct()
    {
        $settings = Setting::whereIn('key', ['lock_user_device'])->get(['key', 'value'])->keyBy('key')
        ->transform(function ($setting) {
            return $setting->value;
        })->toArray();
        $fileService = new FileService();

        $this->settings = $settings;
        $this->fileService = $fileService;
    }

    public function login($request)
    {
        $user_device = $request->header('user-device');
        
        // Required User Device
        if (!isset($user_device)) {
            throw new \Exception('Please Input user-device in header', 400);
        }

        if ($token = auth('employees')->attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = auth('employees')->user();
            $employee = Employee::with(['employment_status_detail'])->where('user_id', $user->id)->first();
            $exceptUser = explode(',', env('user_tester', ''));

            // Validate If Employee Exists
            if (!isset($employee)) {
                throw new \Exception('Alamat email belum terdaftar, silahkan hubungi admin untuk info lebih lanjut', 403);
            }

            // Validate User Device 
            if ($this->settings['lock_user_device'] == 1) {
                if ($user->user_device == null || $user->user_device == '' || $user->user_device == ' ') {
                    $user->update(['user_device' => $user_device]);
                } elseif ($user->user_device != $user_device && !in_array($user->email, $exceptUser)) {
                    throw new \Exception('Akun anda sudah terdaftar dengan HP lain, silahkan hubungi admin', 403);
                }
            }

            // Validate Contract Employee
            $today = Carbon::now()->format('Y-m-d');
            $end_date = Carbon::parse($employee->end_date)->format('Y-m-d');
            if ($employee->employment_status_detail->pkwt_type === 'pkwt' && $today > $end_date) {
                throw new \Exception('Your contract is finished. Can not login', 400);
            }

            $data = $user;
            $data['token'] = $token;
            $data['image'] = $employee->image ? $this->fileService->getFileById($employee->image)->full_path : null;

            return $data;
        } else {
            throw new \Exception('Email atau Password Anda salah, hub admin bila ingin merubahnya', 400);
        }
    }

    public function updateFcmToken($request)
    {
        $input = $request->only(['fcm_token']);
        $user = auth('employees')->user();
        $user->update($input);

        return $user;
    }
}
