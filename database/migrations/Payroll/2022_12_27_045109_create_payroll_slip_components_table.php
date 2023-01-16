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
        Schema::create('payroll_slip_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_employee_slip_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('payroll_component_id')->constrained()->cascadeOnUpdate();
            $table->decimal('value', 48, 4)->nullable();
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
        Schema::dropIfExists('payroll_slip_components');
    }
};
