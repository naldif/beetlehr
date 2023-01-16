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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('leave_type_id')->constrained()->cascadeOnUpdate();
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason');
            $table->string('status');
            $table->text('reject_reason')->nullable();
            $table->bigInteger('file')->nullable();
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
        Schema::dropIfExists('leaves');
    }
};
