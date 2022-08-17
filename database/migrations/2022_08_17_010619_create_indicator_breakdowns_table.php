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
        Schema::create('indicator_breakdowns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('performance_indicator_id')->constrained('performance_indicators')->cascadeOnDelete();
            $table->integer('quarter');
            $table->string('month');
            $table->float('number');
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
        Schema::dropIfExists('indicator_breakdowns');
    }
};