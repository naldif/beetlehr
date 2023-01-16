<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PayrollComponent;

class PayrollComponentSeeder extends Seeder
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
                'name' => 'Potongan Terlambat',
                'type' => 'deduction',
                'is_mandatory' => true,
                'is_editable' => false,
                'is_taxable' => false,
                'custom_attribute' => [
                    'action' => 'deduction_late'
                ]
            ],
        ];

        foreach ($datas as $key => $row) {
            try {
                PayrollComponent::create([
                    'id' => $key + 1,
                    'name' => $row['name'],
                    'type' => $row['type'],
                    'is_mandatory' => $row['is_mandatory'],
                    'is_editable' =>  $row['is_editable'],
                    'is_taxable' => $row['is_taxable'],
                    'custom_attribute' => $row['custom_attribute']
                ]);
            } catch (\Exception $exception) {
                // Do something when the exception is thrown
            }
        }
    }
}
