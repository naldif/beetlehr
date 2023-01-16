<?php

namespace Database\Seeders;

use App\Models\PphRangeRule;
use Illuminate\Database\Seeder;

class PphRangeRuleSeeder extends Seeder
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
                'start_range' => 0,
                'end_range' => 50000000,
                'percentage' => 5,
                'rate_layer' => 1
            ],
            [
                'start_range' => 50000000,
                'end_range' => 250000000,
                'percentage' => 15,
                'rate_layer' => 2
            ],
            [
                'start_range' => 250000000,
                'end_range' => 500000000,
                'percentage' => 25,
                'rate_layer' => 3
            ],
            [
                'start_range' => 500000000,
                'end_range' => 5000000000,
                'percentage' => 30,
                'rate_layer' => 4
            ]
        ];

        foreach ($datas as $key => $value) {
            try {
                PphRangeRule::create([
                    'id' => $key + 1,
                    'start_range' => $value['start_range'],
                    'end_range' => $value['end_range'],
                    'percentage' => $value['percentage'],
                    'rate_layer' => $value['rate_layer']
                ]);
            } catch (\Exception $exception) {
                // Do something when the exception is thrown
            }
        }
    }
}
