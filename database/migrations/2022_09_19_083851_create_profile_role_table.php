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
        Schema::create('profile_role', function (Blueprint $table) {
            $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();
            $table->foreignId('sidebar_role_id')->constrained('sidebar_roles')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profile_role');
    }
};
