<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('rooms_photos')) {
            Schema::create('rooms_photos', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('roomID')->unsigned();
                $table->text('file')->nullable();
                $table->tinyInteger('default')->default(0);
                $table->integer('sort')->default(0);
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
        Schema::dropIfExists('rooms_photos');
    }
}
