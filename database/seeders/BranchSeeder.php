<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
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
                'name' => 'HQ',
                'address' => 'Jl Dummy',
                'state' => 'Jawa Tengah',
                'city' => 'Semarang',
                'zip_code' => '52212',
                'latitude' => '-5.836311069120119',
                'longitude' => '112.66920386677617',
                'radius' => 500,
                'timezone' => 'Asia/Jakarta',
                'npwp_list_id' => 1
            ],
            [
                'id' => 2,
                'name' => 'HM',
                'address' => 'Jl Dummy',
                'state' => 'Jawa Tengah',
                'city' => 'Semarang',
                'zip_code' => '52212',
                'latitude' => '-5.836311069120119',
                'longitude' => '112.66920386677617',
                'radius' => 500,
                'timezone' => 'Asia/Jakarta',
                'npwp_list_id' => 1
            ],
        ];

        foreach ($datas as $data) {
            try {
                Branch::create([
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'address' => $data['address'],
                    'state' => $data['state'],
                    'city' => $data['city'],
                    'zip_code' => $data['zip_code'],
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'radius' => $data['radius'],
                    'timezone' => $data['timezone'],
                    'npwp_list_id' => $data['npwp_list_id']
                ]);
            } catch (\Exception $exception) {
                // Do something when the exception is thrown
            }
        }
    }
}
