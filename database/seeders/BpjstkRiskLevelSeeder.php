<?php

namespace Database\Seeders;

use App\Models\BpjstkRiskLevel;
use Illuminate\Database\Seeder;

class BpjstkRiskLevelSeeder extends Seeder
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
                'name' => 'Sangat Rendah',
                'precentage' => 0.24
            ],
            [
                'name' => 'Rendah',
                'precentage' => 0.54
            ],
            [
                'name' => 'Sedang',
                'precentage' => 0.89
            ],
            [
                'name' => 'Tinggi',
                'precentage' => 1.27
            ],
            [
                'name' => 'Sangat Tinggi',
                'precentage' => 1.74
            ],
        ];

        foreach ($datas as $key => $row) {
            try {
                BpjstkRiskLevel::create([
                    'id' => $key + 1,
                    'name' => $row['name'],
                    'precentage' => $row['precentage']
                ]);
            } catch (\Exception $exception) {
                // Do something when the exception is thrown
            }
        }
    }
}
