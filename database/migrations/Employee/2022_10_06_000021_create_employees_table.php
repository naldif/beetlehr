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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            // Basic Information
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnUpdate();
            $table->foreignId('branch_id')->nullable()->constrained()->cascadeOnUpdate();
            $table->foreignId('designation_id')->nullable()->constrained()->cascadeOnUpdate();
            $table->string('phone_number')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('address');
            $table->string('account_number');
            $table->string('nip')->unique();
            $table->foreignId('image')->nullable()->constrained('files')->cascadeOnUpdate();
            $table->integer('employee_input_order')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender')->nullable();

            // Payroll related data
            $table->foreignId('ptkp_tax_list_id')->nullable()->constrained()->cascadeOnUpdate();
            $table->foreignId('employment_status_id')->nullable()->constrained()->cascadeOnUpdate();
            $table->foreignId('payroll_group_id')->nullable()->constrained()->cascadeOnUpdate();
            $table->boolean('is_use_bpjsk')->default(false);
            $table->string('bpjsk_number_card')->nullable();
            $table->foreignId('bpjsk_setting_id')->nullable()->constrained()->cascadeOnUpdate();
            $table->decimal('bpjsk_specific_amount', 48, 4)->nullable();
            $table->boolean('is_use_bpjstk')->default(false);
            $table->string('bpjstk_number_card')->nullable();
            $table->foreignId('bpjstk_setting_id')->nullable()->constrained()->cascadeOnUpdate();
            $table->boolean('bpjstk_old_age')->default(false);
            $table->boolean('bpjstk_life_insurance')->default(false);
            $table->boolean('bpjstk_pension_time')->default(false);
            $table->decimal('bpjstk_specific_amount', 48, 4)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
