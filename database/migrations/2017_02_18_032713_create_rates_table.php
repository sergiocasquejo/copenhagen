<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('rates')) {
            Schema::create('rates', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('rateCode');
                $table->string('roomCode');
                $table->string('mealType');
                $table->string('description')->nullable();
                $table->integer('isMonthly')->default(0);
                $table->integer('active')->default(1);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('rates')) {
            Schema::dropIfExists('rates');
        }
    }
}
