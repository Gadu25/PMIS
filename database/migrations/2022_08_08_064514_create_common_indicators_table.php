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
        Schema::create('common_indicators', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->mediumText('description');
            $table->foreignId('workshop_id')->constrained('workshops')->onCascadeDelete();
            $table->foreignId('program_id')->constrained('programs')->onCascadeDelete();
            $table->foreignId('subprogram_id')->nullable()->constrained('subprograms')->onCascadeDelete();
            $table->foreignId('cluster_id')->nullable()->constrained('clusters')->onCascadeDelete();
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
        Schema::dropIfExists('common_indicators');
    }
};
