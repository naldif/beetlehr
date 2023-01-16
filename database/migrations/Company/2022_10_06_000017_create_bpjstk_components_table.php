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
        Schema::create('bpjstk_components', function (Blueprint $table) {
            $table->id();
            $table->string('key_name');
            $table->string('name');
            $table->decimal('company_precentage');
            $table->decimal('employee_precentage');
            $table->timestamps();
            $table->softDeletes();
        });

        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        $seederData = [
            [
                'key_name' => 'old_age',
                'name' => 'Jaminan Hari Tua',
                'company_precentage' => 3.7,
                'employee_precentage' => 2,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'key_name' => 'life_insurance',
                'name' => 'Jaminan Kematian',
                'company_precentage' => 0.3,
                'employee_precentage' => 0,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'key_name' => 'pension_time',
                'name' => 'Jaminan Pensiun',
                'company_precentage' => 2,
                'employee_precentage' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
        ];
        DB::table('bpjstk_components')->insert($seederData);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bpjstk_components');
    }
};
