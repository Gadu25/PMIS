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
        Schema::create('annex_ones', function (Blueprint $table) {
            $table->id();
            $table->string('source_of_funds');
            $table->string('header_type');
            $table->foreignId('workshop_id')->constrained('workshops')->onCascadeDelete();
            $table->foreignId('project_id')->constrained('projects')->onCascadeDelete();
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
        Schema::dropIfExists('annex_ones');
    }
};
