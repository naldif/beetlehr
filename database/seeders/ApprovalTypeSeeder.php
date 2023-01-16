<?php

namespace Database\Seeders;

use App\Models\ApprovalType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ApprovalTypeSeeder extends Seeder
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
                'label' => 'Create Leave',
                'type' => 'create_leave',
                'group' => 'leave'
            ],
            [
                'label' => 'Attendance Without Schedule',
                'type' => 'attendance_without_schedule',
                'group' => 'attendance'
            ]
        ];

        foreach ($datas as $key => $row) {
            try {
                ApprovalType::create([
                    'id' => $key + 1,
                    'label' => $row['label'],
                    'type' => $row['type'],
                    'group' => $row['group'],
                ]);
            } catch (\Exception $exception) {
                // Do something when the exception is thrown
            }
        }
    }
}
