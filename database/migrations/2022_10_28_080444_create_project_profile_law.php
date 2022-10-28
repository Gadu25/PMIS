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
        Schema::create('project_profile_law', function (Blueprint $table) {
            $table->foreignId('project_profile_id')->constrained('project_profiles')->cascadeOnDelete();
            $table->foreignId('law_id')->constrained('laws')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_profile_law');
    }
};
