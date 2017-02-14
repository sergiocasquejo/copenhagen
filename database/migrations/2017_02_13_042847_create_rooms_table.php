<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('rooms')) {
            Schema::create('rooms', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 255)->unique();
                $table->string('slug', 255)->unique();
                $table->string('totalRooms')->unique();
                $table->double('standardRate')->default(0);
                $table->double('minimumRate')->default(0);
                $table->integer('totalPerson')->default(1);
                $table->integer('extraBed')->default(0);
                $table->integer('roomSize')->nullable();
                $table->integer('bathrooms')->default(0);
                $table->string('building')->default('Main');
                $table->tinyInteger('isActive')->default(1);
                $table->integer('sort')->default(0);
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
        Schema::dropIfExists('rooms');
    }
}
