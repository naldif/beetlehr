<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmploymentStatus;

class EmploymentStatusSeeder extends Seeder
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
                'name' => 'Full Time',
                'pkwt_type' => 'pkwtt',
                'status' => true,
                'employment_type' => 'pegawai tetap'
            ],
        ];

        foreach ($datas as $key => $value) {
            try {
                EmploymentStatus::create([
                    'id' => $key + 1,
                    'name' => $value['name'],
                    'pkwt_type' => $value['pkwt_type'],
                    'status' => $value['status'],
                    'employment_type' => $value['employment_type']
                ]);
            } catch (\Exception $exception) {
                // Do something when the exception is thrown
            }
        }
    }
}
