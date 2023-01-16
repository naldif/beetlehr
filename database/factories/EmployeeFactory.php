<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Branch;
use App\Models\Designation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // Create New User
        $user = User::create([
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('rahasia123')
        ]);

        // FirstOrCreate Branch
        $branch = Branch::firstOrCreate([
            'id' => 1
        ], [
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
        ]);

        // FirstOrCreate Designation
        $designation = Designation::firstOrCreate([
            'id' => 1
        ], [
            'id' => 1,
            'name' => 'Team Leader'
        ]);

        return [
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'designation_id' => $designation->id,
            'phone_number' => fake()->phoneNumber(),
            'start_date' => '2022-01-01',
            'end_date' => '2070-01-01',
            'address' => 'Jl Impian',
            'account_number' => '12412312312',
            'employee_external_id' => '21412312312'
        ];
    }
}
