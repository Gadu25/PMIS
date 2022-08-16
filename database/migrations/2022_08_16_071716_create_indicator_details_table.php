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
        Schema::create('indicator_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('indicatorable_id');
            $table->string('indicatorable_type');
            $table->integer('actual');
            $table->integer('estimate');
            $table->integer('physical_targets');
            $table->integer('first');
            $table->integer('second');
            $table->integer('third');
            $table->integer('fourth');
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
        Schema::dropIfExists('indicator_details');
    }
};
