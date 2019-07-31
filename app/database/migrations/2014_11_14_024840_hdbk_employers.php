<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class HdbkEmployers extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::create(
            'employers', function (Blueprint $table) {
                $table->increments('id');
                $table->smallInteger('city_id');
                $table->integer('image_id')->nullable();
                $table->string('name');
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('icq')->nullable();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::drop('employers');
    }

}
