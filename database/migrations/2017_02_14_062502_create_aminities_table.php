<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAminitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('aminities')) {
            Schema::create('aminities', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
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
        if (Schema::hasTable('aminities')) {
            Schema::dropIfExists('aminities');
        }
    }
}
