<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'module_name' => 'Employee',
                'description' => 'Module for manage employee',
                'status' => 'inActive',
                'version' => '1.0.0'
            ]
        ];

        foreach ($data as $row) {
            try {
                Module::create([
                    'id' => $row['id'],
                    'module_name' => $row['module_name'],
                    'description' => $row['description'],
                    'status' => $row['status'],
                    'version' => $row['version']
                ]);
            } catch (\Exception $exception) {
                // Not doing anything when module exists
                $message = '  Module ' . $row['module_name'] . ' already exists. Do you want to update this module? [y/n]';
                $ask = $this->command->ask($message);

                if ($ask == 'yes' || $ask == 'y') {
                    Module::where('id', $row["id"])->update(
                        [
                            'module_name' => $row['module_name'],
                            'description' => $row['description'],
                            'status' => $row['status'],
                            'version' => $row['version']
                        ]
                    );
                }
            }
        }
    }
}
