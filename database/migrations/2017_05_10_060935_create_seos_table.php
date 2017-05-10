<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('seoableId')->unsigned();
            $table->string('seoableType', 250)->default('room');
            $table->string('metaTitle', 70);
            $table->string('slug', 250);
            $table->text('metaKeywords')->nullable();
            $table->text('metaDescription')->nullable();
            $table->string('h1Tag', 250)->nullable();
            $table->string('redirect301', 250)->nullable();
            $table->string('canonicalLinks', 250)->nullable();
            $table->integer('metaRobotTag')->default(0);// index(Default)|No Index
            $table->string('metaRobotFollow')->default(0);//Follow(Default)|No Follow

            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seos');
    }
}
