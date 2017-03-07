<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('bookings')) {
            Schema::create('bookings', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('roomID')->unsigned();
                $table->string('refId')->unique();
                $table->integer('customerID')->unsigned();
                $table->date('checkIn');
                $table->string('checkInTime');
                $table->date('checkOut');
                $table->string('checkOutTime');
                $table->integer('noOfRooms')->default(1);
                $table->integer('noOfNights')->default(1);
                $table->integer('noOfAdults')->default(1);
                $table->integer('noOfChild')->default(0)->nullable();
                $table->double('roomRate')->default(0);
                $table->double('totalAmount')->default(0);
                $table->integer('rateId')->unsigned();
                $table->string('specialInstructions')->nullable();
                $table->string('billingInstructions')->nullable();
                $table->string('status')->default('pending');
                $table->timestamps();
                $table->foreign('roomID')->references('id')->on('rooms')
                ->onDelete('cascade');
                $table->foreign('customerID')->references('id')->on('customers')
                ->onDelete('cascade');
                $table->foreign('rateId')->references('id')->on('rates')
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
        if (Schema::hasTable('bookings')) {
            Schema::dropIfExists('bookings');
        }
    }
}
