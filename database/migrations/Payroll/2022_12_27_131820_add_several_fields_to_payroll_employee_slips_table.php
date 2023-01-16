<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payroll_employee_slips', function (Blueprint $table) {
            $table->decimal('bpjsk_value', 48, 4)->nullable();
            $table->decimal('jkk', 48, 4)->nullable();
            $table->decimal('jht', 48, 4)->nullable();
            $table->decimal('jkm', 48, 4)->nullable();
            $table->decimal('jp', 48, 4)->nullable();
            $table->decimal('tax_value', 48, 4)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payroll_employee_slips', function (Blueprint $table) {
            $table->dropColumn(['bpjsk_value', 'jkk', 'jht', 'jkm', 'jp', 'tax_value']);
        });
    }
};
