<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Designation;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Actions\Utility\Employee\CalculateEmployeeExternalId;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');

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

        $total = $this->command->ask('How much employee you want to generated?');

        try {
            for ($i = 0; $i < $total; $i++) {
                DB::beginTransaction();
                // Create New User
                $user = User::create([
                    'name' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                    'email_verified_at' => now(),
                    'password' => Hash::make('rahasia123')
                ]);
                $user->assignRole('employee');

                // Calculate Employee External Id
                $generateExternalId = new CalculateEmployeeExternalId();
                $employeeOrderId = $generateExternalId->calculateIncrementalEmployeeId();

                Employee::create([
                    'user_id' => $user->id,
                    'branch_id' => $branch->id,
                    'designation_id' => $designation->id,
                    'phone_number' => $faker->e164PhoneNumber,
                    'start_date' => '2022-01-01',
                    'end_date' => '2070-01-01',
                    'address' => 'Jl Impian',
                    'account_number' => '12412312312',
                    'employee_external_id' => $generateExternalId->handle(Carbon::now()->format('Y-m-d')),
                    'employee_input_order' => $employeeOrderId
                ]);
                DB::commit();
            }
        } catch (\Throwable $th) {
            DB::rollBack();
        }
    }
}
