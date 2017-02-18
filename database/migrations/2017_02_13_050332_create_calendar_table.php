<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalendarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('calendar')) {
            Schema::create('calendar', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('roomID')->unsigned();
                $table->date('selectedDate');
                $table->double('minimumPrice')->default(0);
                $table->integer('availability')->default(0);
                $table->integer('isActive')->default(0);
                $table->integer('onCreateSetup')->default(0);
                $table->timestamps();
                $table->foreign('roomID')->references('id')->on('rooms')
                ->onDelete('cascade');;
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
        Schema::dropIfExists('rooms_photos');
    }
}
