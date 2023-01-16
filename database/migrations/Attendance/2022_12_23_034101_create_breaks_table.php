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
        Schema::create('breaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->time('total_work_hours')->nullable();
            $table->string('latitude_start_break')->nullable();
            $table->string('longitude_start_break')->nullable();
            $table->string('latitude_end_break')->nullable();
            $table->string('longitude_end_break')->nullable();
            $table->string('address_start_break')->nullable();
            $table->string('address_end_break')->nullable();
            $table->string('note_start_break')->nullable();
            $table->string('note_end_break')->nullable();
            $table->bigInteger('image_id_start_break')->nullable();
            $table->bigInteger('image_id_end_break')->nullable();
            $table->json('files_start_break')->nullable();
            $table->json('files_end_break')->nullable();
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
        Schema::dropIfExists('breaks');
    }
};
