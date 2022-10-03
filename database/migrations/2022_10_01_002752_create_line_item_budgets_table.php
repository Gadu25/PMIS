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
        Schema::create('line_item_budgets', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->string('source_of_funds');
            $table->foreignId('project_profile_id')->constrained('project_profiles')->cascadeOnDelete();
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
        Schema::dropIfExists('line_item_budgets');
    }
};
