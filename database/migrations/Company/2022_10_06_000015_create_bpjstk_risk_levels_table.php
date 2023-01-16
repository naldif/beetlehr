<?php

use Carbon\Carbon;
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
        Schema::create('bpjstk_risk_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('precentage');
            $table->timestamps();
            $table->softDeletes();
        });

        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        $seederData = [
            [
                'name' => 'Sangat Rendah',
                'precentage' => 0.24,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'name' => 'Rendah',
                'precentage' => 0.54,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'name' => 'Sedang',
                'precentage' => 0.89,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'name' => 'Tinggi',
                'precentage' => 1.27,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'name' => 'Sangat Tinggi',
                'precentage' => 1.74,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
        ];
        DB::table('bpjstk_risk_levels')->insert($seederData);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bpjstk_risk_levels');
    }
};
