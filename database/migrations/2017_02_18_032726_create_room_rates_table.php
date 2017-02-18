<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('room_rates')) {
            Schema::create('room_rates', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('rateID')->unsigned();
                $table->integer('roomID')->unsigned();
                $table->double('price')->default(0);
                $table->foreign('rateID')->references('id')->on('rates')
                ->onDelete('cascade');
                $table->foreign('roomID')->references('id')->on('rooms')
                ->onDelete('cascade');
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
        if (Schema::hasTable('room_rates')) {
            Schema::dropIfExists('room_rates');
        }
    }
}
