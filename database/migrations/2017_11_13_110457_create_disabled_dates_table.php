<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDisabledDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('disabled_dates')) {
            Schema::create('disabled_dates', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('room_id')->unsigned();
                $table->date('selected_date');
                $table->timestamps();
                $table->foreign('room_id')->references('id')->on('rooms');
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
        Schema::dropIfExists('disabled_dates');
    }
}
