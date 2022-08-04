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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('num');
            $table->mediumText('title');
            $table->string('status');
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->foreignId('subprogram_id')->nullable()->constrained('subprograms')->cascadeOnDelete();
            $table->foreignId('cluster_id')->nullable()->constrained('clusters')->cascadeOnDelete();
            $table->foreignId('division_id')->constrained('divisions')->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->cascadeOnDelete();
            $table->foreignId('subunit_id')->nullable()->constrained('subunits')->cascadeOnDelete();
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
        Schema::dropIfExists('projects');
    }
};
