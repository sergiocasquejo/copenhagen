<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsAminitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('rooms_aminities')) {
            Schema::create('rooms_aminities', function (Blueprint $table) {
                $table->integer('roomID')->unsigned();
                $table->integer('aminitiesID')->unsigned();
                $table->foreign('roomID')->references('id')->on('rooms')
                ->onDelete('cascade');
                $table->foreign('aminitiesID')->references('id')->on('aminities')
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
        if (Schema::hasTable('rooms_aminities')) {
            Schema::dropIfExists('rooms_aminities');
        }
    }
}
