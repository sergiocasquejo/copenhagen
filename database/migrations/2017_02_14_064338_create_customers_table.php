<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('userID')->nullable();
                $table->string('email', 250)->unique();
                $table->string('salutation')->default('Mr');
                $table->string('firstName');
                $table->string('lastName');
                $table->string('middleName')->nullable();
                $table->string('address1');
                $table->string('address2')->nullable();
                $table->string('state');
                $table->string('city');
                $table->string('zipcode');
                $table->string('countryCode');
                $table->string('contact');
                $table->timestamps();
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
        if (Schema::hasTable('customers')) {
            Schema::dropIfExists('customers');
        }
    }
}
