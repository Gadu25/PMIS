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
        Schema::create('annex_e_subs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('annex_e_id')->constrained('annex_e_s')->cascadeOnDelete();
            $table->foreignId('subproject_id')->constrained('subprojects')->cascadeOnDelete();
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
        Schema::dropIfExists('annex_e_subs');
    }
};
