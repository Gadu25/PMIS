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
        Schema::create('lib_items', function (Blueprint $table) {
            $table->id();
            $table->mediumText('item');
            $table->float('amount', 12, 2);
            $table->string('type');
            $table->foreignId('lib_id')->constrained('line_item_budgets')->cascadeOnDelete();
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
        Schema::dropIfExists('lib_items');
    }
};
