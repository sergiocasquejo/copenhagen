<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('bookingID')->unsigned();
                $table->double('totalAmount')->default(0);
                $table->string('method');
                $table->string('status')->default('pending');
                $table->string('referenceID')->nullable();
                $table->string('customData')->nullable();
                $table->timestamps();
                $table->foreign('bookingID')->references('id')->on('bookings')
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
        if (Schema::hasTable('payments')) {
            Schema::dropIfExists('payments');
        }
    }
}
