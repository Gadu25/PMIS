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
        Schema::create('sidebar_roles', function (Blueprint $table) {
            $table->id();
            $table->mediumText('title');
            $table->string('code');
            $table->mediumText('description');
            $table->foreignId('sidebar_item_id')->constrained('sidebar_items')->cascadeOnDelete();
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
        Schema::dropIfExists('sidebar_roles');
    }
};
