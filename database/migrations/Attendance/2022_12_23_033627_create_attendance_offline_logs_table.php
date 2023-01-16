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
        Schema::create('attendance_offline_logs', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_offline_clock_in')->default(false);
            $table->boolean('is_offline_clock_out')->default(false);
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate();
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->date('date_clock')->nullable();
            $table->string('latitude_clock_in')->nullable();
            $table->string('longitude_clock_in')->nullable();
            $table->string('latitude_clock_out')->nullable();
            $table->string('longitude_clock_out')->nullable();
            $table->bigInteger('image_id_clock_in')->nullable();
            $table->bigInteger('image_id_clock_out')->nullable();
            $table->json('files_clock_in')->nullable();
            $table->json('files_clock_out')->nullable();
            $table->text('notes_clock_in')->nullable();
            $table->text('notes_clock_out')->nullable();
            $table->text('address_clock_in')->nullable();
            $table->text('address_clock_out')->nullable();
            $table->time('total_work_hours')->nullable();
            $table->string('status')->nullable();
            $table->string('reject_reason')->nullable();
            $table->string('type')->nullable();
            $table->string('source_clock_in')->nullable();
            $table->string('reference_clock_in')->nullable();
            $table->string('source_clock_out')->nullable();
            $table->string('reference_clock_out')->nullable();
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
        Schema::dropIfExists('attendance_offline_logs');
    }
};
