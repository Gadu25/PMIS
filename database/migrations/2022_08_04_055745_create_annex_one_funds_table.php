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
        Schema::create('annex_one_funds', function (Blueprint $table) {
            $table->id();
            $table->float('amount', 12, 2);
            $table->integer('year');
            $table->bigInteger('fundable_id');
            $table->string('fundable_type');
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
        Schema::dropIfExists('annex_one_funds');
    }
};
