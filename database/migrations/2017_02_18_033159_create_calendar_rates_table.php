<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalendarRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('calendar_rates')) {
            Schema::create('calendar_rates', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('rateID')->unsigned();
                $table->integer('calendarID')->unsigned();
                $table->double('price')->default(0);
                $table->string('active')->default(1);
                $table->foreign('rateID')->references('id')->on('rates')
                ->onDelete('cascade');
                $table->foreign('calendarID')->references('id')->on('calendar')
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
        if (Schema::hasTable('calendar_rates')) {
            Schema::dropIfExists('calendar_rates');
        }
    }
}
