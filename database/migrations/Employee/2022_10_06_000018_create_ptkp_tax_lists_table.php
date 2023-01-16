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
        Schema::create('ptkp_tax_lists', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->string('name');
            $table->integer('value');
            $table->timestamps();
            $table->softDeletes();
        });

        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        $seeder = [
            [
                'name' => 'TK/0',
                'description' => 'Tidak Kawin Tanpa Tanggungan',
                'value' => '54000000',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'name' => 'TK/1',
                'description' => 'Tidak Kawin 1 Tanggungan',
                'value' => '58500000',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'name' => 'TK/2',
                'description' => 'Tidak Kawin 2 Tanggungan',
                'value' => '63000000',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'name' => 'TK/3',
                'description' => 'Tidak Kawin 3 Tanggungan',
                'value' => '67500000',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'name' => 'K/0',
                'description' => 'Kawin Tanpa Tanggungan',
                'value' => '58500000',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'name' => 'K/1',
                'description' => 'Kawin 1 Tanggungan',
                'value' => '63000000',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'name' => 'K/2',
                'description' => 'Kawin 2 Tanggungan',
                'value' => '67500000',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'name' => 'K/3',
                'description' => 'Kawin 3 Tanggungan',
                'value' => '72000000',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'name' => 'HB/0',
                'description' => 'Kawin Penghasilan Istri Digabung dengan Suami Tanpa Tanggungan',
                'value' => '112500000',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'name' => 'HB/1',
                'description' => 'Kawin Penghasilan Istri Digabung dengan Suami 1 Tanggungan',
                'value' => '117000000',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'name' => 'HB/2',
                'description' => 'Kawin Penghasilan Istri Digabung dengan Suami 2 Tanggungan',
                'value' => '121500000',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
            [
                'name' => 'HB/3',
                'description' => 'Kawin Penghasilan Istri Digabung dengan Suami 3 Tanggungan',
                'value' => '126000000',
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ],
        ];

        DB::table('ptkp_tax_lists')->insert($seeder);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ptkp_tax_lists');
    }
};
