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
        Schema::create('approval_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->bigInteger('approver_id')->nullable();
            $table->json('approver')->nullable();
            $table->integer('level');
            $table->string('status');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('void_at')->nullable();
            $table->timestamp('done_at')->nullable();
            $table->string('rejected_reason')->nullable();
            $table->string('approved_reason')->nullable();
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
        Schema::dropIfExists('approval_steps');
    }
};
