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
        Schema::create('payroll_employee_slips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('payroll_slip_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('payroll_employee_base_salary_id')->nullable()->constrained()->cascadeOnUpdate();
            $table->decimal('amount', 48, 4)->nullable();
            $table->decimal('earning_total', 48, 4)->nullable();
            $table->decimal('deduction_total', 48, 4)->nullable();
            $table->decimal('total_amount', 48, 4)->nullable();
            $table->string('status');
            $table->date('paid_on')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payroll_employee_slips');
    }
};
