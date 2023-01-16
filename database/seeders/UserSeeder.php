<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
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
                'name' => 'Admin',
                'email' => 'admin@beetlehr.com',
                'password' => Hash::make('rahasia123'),
                'role' => ['admin']
            ],
            [
                'name' => 'employee',
                'email' => 'employee@beetlehr.com',
                'password' => Hash::make('rahasia123'),
                'role' => ['employee']
            ]
        ];

        foreach ($data as $row) {
            try {
                $user = User::create([
                    'name' => $row["name"],
                    'email' => $row["email"],
                    'password' => $row["password"],
                ]);

                // Assign to 
                $user->assignRole($row['role']);
            } catch (\Exception $exception) {
                $message = '  Email ' . $row['email'] . ' already exists. Do you want to update this email? [y/n]';
                $ask = $this->command->ask($message);

                if ($ask == 'yes' || $ask == 'y') {
                    $user = User::where('email', $row['email'])->first();

                    $user->update([
                        'name' => $row["name"],
                        'password' => $row["password"],
                    ]);
                    // Assign to 
                    $user->assignRole($row['role']);
                }
            }
        }
    }
}
