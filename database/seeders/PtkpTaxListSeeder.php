<?php

namespace Database\Seeders;

use App\Models\PtkpTaxList;
use Illuminate\Database\Seeder;

class PtkpTaxListSeeder extends Seeder
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
                'name' => 'TK/0',
                'description' => 'Tidak Kawin Tanpa Tanggungan',
                'value' => '54000000'
            ],
            [
                'name' => 'TK/1',
                'description' => 'Tidak Kawin 1 Tanggungan',
                'value' => '58500000'
            ],
            [
                'name' => 'TK/2',
                'description' => 'Tidak Kawin 2 Tanggungan',
                'value' => '63000000'
            ],
            [
                'name' => 'TK/3',
                'description' => 'Tidak Kawin 3 Tanggungan',
                'value' => '67500000'
            ],
            [
                'name' => 'K/0',
                'description' => 'Kawin Tanpa Tanggungan',
                'value' => '58500000'
            ],
            [
                'name' => 'K/1',
                'description' => 'Kawin 1 Tanggungan',
                'value' => '63000000'
            ],
            [
                'name' => 'K/2',
                'description' => 'Kawin 2 Tanggungan',
                'value' => '67500000'
            ],
            [
                'name' => 'K/3',
                'description' => 'Kawin 3 Tanggungan',
                'value' => '72000000'
            ],
            [
                'name' => 'HB/0',
                'description' => 'Kawin Penghasilan Istri Digabung dengan Suami Tanpa Tanggungan',
                'value' => '112500000'
            ],
            [
                'name' => 'HB/1',
                'description' => 'Kawin Penghasilan Istri Digabung dengan Suami 1 Tanggungan',
                'value' => '117000000'
            ],
            [
                'name' => 'HB/2',
                'description' => 'Kawin Penghasilan Istri Digabung dengan Suami 2 Tanggungan',
                'value' => '121500000'
            ],
            [
                'name' => 'HB/3',
                'description' => 'Kawin Penghasilan Istri Digabung dengan Suami 3 Tanggungan',
                'value' => '126000000'
            ],
        ];

        foreach ($datas as $key => $row) {
            try {
                PtkpTaxList::create([
                    'id' => $key + 1,
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'value' => $row['value']
                ]);
            } catch (\Exception $exception) {
                // Do something when the exception is thrown
            }
        }
    }
}
