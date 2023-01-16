<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiBaseController;

class UserDevice extends ApiBaseController
{
    public function __construct()
    {
        $settings = Setting::whereIn('key', ['lock_user_device'])->get(['key', 'value'])->keyBy('key')
            ->transform(function ($setting) {
                return $setting->value;
            })->toArray();

        $this->settings = $settings;
    }
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->settings['lock_user_device'] == 1) {
            $key = $request->header('user-device');
            $exceptUser = explode(',', env('user_tester', ''));
            $user = auth('employees')->user();

            if (!isset($key)) {
                return $this->exceptionError('Please input user-device in header', 400);
            } elseif ($user->user_device != $key && !in_array($user->email, $exceptUser)) {
                return $this->exceptionError('Invalid User-Device', 403);
            }
        }

        return $next($request);
    }
}
