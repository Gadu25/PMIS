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
        Schema::create('project_profiles', function (Blueprint $table) {
            $table->id();
            $table->mediumText('title');
            $table->string('status');
            $table->string('compliance_with_law');
            $table->string('project_leader');
            $table->integer('start');
            $table->integer('end');
            $table->integer('year');
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('profiles')->cascadeOnDelete();
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
        Schema::dropIfExists('project_profiles');
    }
};
