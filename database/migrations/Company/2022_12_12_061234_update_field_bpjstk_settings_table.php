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
        Schema::table('bpjstk_settings', function (Blueprint $table) {
            $table->boolean('old_age')->default(false)->change();
            $table->boolean('life_insurance')->default(false)->change();
            $table->boolean('pension_time')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bpjstk_settings', function (Blueprint $table) {
            $table->boolean('old_age')->change();
            $table->boolean('life_insurance')->change();
            $table->boolean('pension_time')->change();
        });
    }
};
