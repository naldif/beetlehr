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
        Schema::create('employee_resigns', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_according_procedure')->default(false);
            $table->foreignId('employee_id')->constrained()->cascadeOnUpdate();
            $table->date('date');
            $table->date('end_contract');
            $table->text('reason');
            $table->foreignId('file')->constrained('files')->cascadeOnUpdate();
            $table->string('status');
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
        Schema::dropIfExists('employee_resigns');
    }
};
