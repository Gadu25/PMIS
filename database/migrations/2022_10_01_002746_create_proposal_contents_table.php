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
        Schema::create('proposal_contents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->mediumText('text');
            $table->string('img_filename')->nullable();
            $table->string('img_position')->nullable();
            $table->foreignId('project_profile_id')->constrained('project_profiles')->cascadeOnDelete();
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
        Schema::dropIfExists('proposal_contents');
    }
};
