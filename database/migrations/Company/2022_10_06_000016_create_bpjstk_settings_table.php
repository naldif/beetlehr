<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bpjstk_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('registration_number')->nullable();
            $table->string('bpjs_office')->nullable();
            $table->integer('minimum_value')->nullable();
            $table->boolean('status')->default(false);
            $table->date('valid_month');
            $table->foreignId('bpjstk_risk_level_id')->nullable()->constrained()->cascadeOnUpdate();
            $table->boolean('old_age');
            $table->boolean('life_insurance');
            $table->boolean('pension_time');
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
        Schema::dropIfExists('bpjstk_settings');
    }
};
