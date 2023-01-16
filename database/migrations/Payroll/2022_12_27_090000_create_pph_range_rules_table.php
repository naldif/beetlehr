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
        Schema::create('pph_range_rules', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('start_range');
            $table->bigInteger('end_range');
            $table->decimal('percentage');
            $table->integer('rate_layer');
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
        Schema::dropIfExists('pph_range_rules');
    }
};
