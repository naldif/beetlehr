<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MigrationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom([
            database_path('migrations/Core'),
            database_path('migrations/Company'),
            database_path('migrations/Employee'),
            database_path('migrations/Leave'),
            database_path('migrations/Overtime'),
            database_path('migrations/Payroll'),
            database_path('migrations/Holiday'),
            database_path('migrations/Approval'),
            database_path('migrations/Attendance'),
            database_path('migrations/NoticeBoard'),
        ]);
    }
}
