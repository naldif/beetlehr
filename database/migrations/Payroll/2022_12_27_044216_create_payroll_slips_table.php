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
        Schema::create('payroll_slips', function (Blueprint $table) {
            $table->id();
            $table->decimal('total_amount', 48, 4)->nullable();
            $table->date('date');
            $table->boolean('is_all_branch')->default(false);
            $table->foreignId('branch_id')->nullable()->constrained()->cascadeOnUpdate();
            $table->foreignId('created_by')->constrained('users')->cascadeOnUpdate();
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
        Schema::dropIfExists('payroll_slips');
    }
};
