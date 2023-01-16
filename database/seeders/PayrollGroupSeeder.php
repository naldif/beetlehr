<?php

namespace Database\Seeders;

use App\Models\PayrollGroup;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PayrollGroupSeeder extends Seeder
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
                'name' => 'Struktural'
            ],
        ];

        foreach ($datas as $key => $value) {
            try {
                PayrollGroup::create([
                    'id' => $key + 1,
                    'name' => $value['name']
                ]);
            } catch (\Exception $exception) {
                // Do something when the exception is thrown
            }
        }
    }
}
