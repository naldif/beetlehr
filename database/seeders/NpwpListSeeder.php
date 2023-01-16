<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\NpwpList;
use Illuminate\Database\Seeder;

class NpwpListSeeder extends Seeder
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
                'id' => 1,
                'npwp_name' => 'Dummy',
                'npwp_company_name' => 'Dummy Corp',
                'number_npwp' => '244412321212',
                'address' => 'Jl Simpang Lima',
                'postal_code' => '52212',
                'city' => 'Semarang',
                'kpp' => 'Dummy',
                'active_month' => Carbon::now(),
                'status' => rand(0,1)
            ],
        ];

        foreach ($datas as $data) {
            try {
                NpwpList::create([
                    'id' => $data['id'],
                    'npwp_name' => $data['npwp_name'],
                    'npwp_company_name' => $data['npwp_company_name'],
                    'number_npwp' => $data['number_npwp'],
                    'address' => $data['address'],
                    'postal_code' => $data['postal_code'],
                    'city' => $data['city'],
                    'kpp' => $data['kpp'],
                    'active_month' => $data['active_month'],
                    'status' => $data['status']
                ]);
            } catch (\Exception $exception) {
                // Do something when the exception is thrown
            }
        }
    }
}
