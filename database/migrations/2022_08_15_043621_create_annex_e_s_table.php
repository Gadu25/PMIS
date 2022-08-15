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
        Schema::create('annex_e_s', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id')->constrained('workshops')->cascadeOnDelete();
            $table->foreignId('project_id')->constrined('projects')->cascadeOnDelete();
            $table->mediumText('remarks')->nullable();
            $table->string('status');
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
        Schema::dropIfExists('annex_e_s');
    }
};
