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
        Schema::create('national_expenditure_subs', function (Blueprint $table) {
            $table->id();
            $table->float('amount', 12, 2);
            $table->boolean('isAdded')->default(false);
            $table->foreignId('national_expenditure_id')->constrained('national_expenditures')->cascadeOnDelete();
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
        Schema::dropIfExists('national_expenditure_subs');
    }
};
