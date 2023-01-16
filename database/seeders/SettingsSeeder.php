<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            [
                'key' => 'date_reset_leave_quota',
                'value' => '2023-01-01',
            ],
            [
                'key' => 'close_break_page',
                'value' => '1'
            ],
            [
                'key' => 'tolerance_notification',
                'value' => '15'
            ],
            [
                'key' => 'tolerance_clock_in',
                'value' => '15'
            ],
            [
                'key' => 'tolerance_clock_out',
                'value' => '18'
            ],
            [
                'key' => 'is_absent_force_clock_out',
                'value' => 0
            ],
            [
                'key' => 'time_for_force_clockout_type',
                'value' => 'minutes'
            ],
            [
                'key' => 'time_for_force_clockout_fixed',
                'value' => null
            ],
            [
                'key' => 'time_for_force_clockout_minutes',
                'value' => '30'
            ],
            [
                'key' => 'payroll_istaxable',
                'value' => '1'
            ],
            [
                'key' => 'lock_user_device',
                'value' => '1'
            ],
            [
                'key' => 'editable_employee_external_id',
                'value' => '1'
            ]
        ];

        foreach ($datas as $key => $value) {
            try {
                Setting::create([
                    'id' => $key + 1,
                    'key' => $value['key'],
                    'value' => $value['value']
                ]);
            } catch (\Exception $exception) {
                // Do something when the exception is thrown
            }
        }
    }
}
