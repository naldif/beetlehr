<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            SettingsSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            ModuleSeeder::class,
            PayrollGroupSeeder::class,
            NpwpListSeeder::class,
            BranchSeeder::class,
            DesignationSeeder::class,
            PtkpTaxListSeeder::class,
            EmploymentStatusSeeder::class,
            BpjstkRiskLevelSeeder::class,
            EmployeeSeeder::class,
            PayrollComponentSeeder::class,
            ApprovalTypeSeeder::class,
            AttendanceShiftSeeder::class,
            PphRangeRuleSeeder::class
        ]);
    }
}
