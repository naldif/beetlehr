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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_force_clock_out')->default(false);
            $table->boolean('is_offline_clock_in')->default(false);
            $table->boolean('is_offline_clock_out')->default(false);
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate();
            $table->string('status')->nullable();
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->date('date_clock')->nullable();
            $table->string('latitude_clock_in')->nullable();
            $table->string('longitude_clock_in')->nullable();
            $table->string('latitude_clock_out')->nullable();
            $table->string('longitude_clock_out')->nullable();
            $table->text('notes_clock_in')->nullable();
            $table->text('notes_clock_out')->nullable();
            $table->json('files_clock_in')->nullable();
            $table->json('files_clock_out')->nullable();
            $table->bigInteger('image_id_clock_in')->nullable();
            $table->bigInteger('image_id_clock_out')->nullable();
            $table->text('address_clock_in')->nullable();
            $table->text('address_clock_out')->nullable();
            $table->boolean('is_outside_radius_clock_in')->default(false);
            $table->boolean('is_outside_radius_clock_out')->default(false);
            $table->boolean('is_need_approval_clock_in')->default(false);
            $table->boolean('is_need_approval_clock_out')->default(false);
            $table->timestamp('clock_in_approved_at')->nullable();
            $table->timestamp('clock_out_approved_at')->nullable();
            $table->foreignId('clock_in_approved_by')->nullable()->constrained('users')->cascadeOnUpdate();
            $table->foreignId('clock_out_approved_by')->nullable()->constrained('users')->cascadeOnUpdate();
            $table->boolean('is_late_clock_in')->default(false);
            $table->boolean('is_early_clock_out')->default(false);
            $table->time('total_work_hours')->nullable();
            $table->time('total_late_clock_in')->nullable();
            $table->time('total_early_clock_out')->nullable();
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
        Schema::dropIfExists('attendances');
    }
};
