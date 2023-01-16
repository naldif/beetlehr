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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('telegram_chat_id')->nullable();
            $table->string('name');
            $table->string('address');
            $table->string('state');
            $table->string('city');
            $table->string('zip_code');
            $table->string('latitude');
            $table->string('longitude');
            $table->integer('radius');
            $table->string('timezone');
            $table->integer('radius_tracker')->nullable();
            $table->time('tracker_interval')->nullable();
            $table->foreignId('npwp_list_id')->constrained()->cascadeOnUpdate();
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
        Schema::dropIfExists('branches');
    }
};
