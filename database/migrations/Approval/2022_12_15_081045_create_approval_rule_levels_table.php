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
        Schema::create('approval_rule_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_rule_id')->constrained('approval_rules')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('approver_type');
            $table->foreignId('employee_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('level_approval');
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
        Schema::dropIfExists('approval_rule_levels');
    }
};
