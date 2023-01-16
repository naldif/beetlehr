<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttendanceShiftSeeder extends Seeder
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
                'branch_id' => 1,
                'name' => 'Shift Normal',
                'is_night_shift' => false,
                'start_time' => '08:00',
                'end_time' => '18:00',
            ],
            [
                'branch_id' => 1,
                'name' => 'Shift Malam',
                'is_night_shift' => true,
                'start_time' => '19:00',
                'end_time' => '02:00',
            ],
        ];

        foreach ($datas as $key => $row) {
            try {
                Shift::create([
                    'id' => $key + 1,
                    'branch_id' => $row['branch_id'],
                    'name' => $row['name'],
                    'is_night_shift' => $row['is_night_shift'],
                    'start_time' => $row['start_time'],
                    'end_time' => $row['end_time'],
                ]);
            } catch (\Exception $exception) {
                // Do something when the exception is thrown
            }
        }
    }
}
