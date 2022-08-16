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
        Schema::create('performance_indicators', function (Blueprint $table) {
            $table->id();
            $table->mediumText('description');
            $table->boolean('common')->default(false);
            $table->bigInteger('indicatorable_id');
            $table->string('indicatorable_type');
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
        Schema::dropIfExists('performance_indicators');
    }
};
