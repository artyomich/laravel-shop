<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Opinions extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'opinions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('image_id')->unsigned()->index();
            $table->string('name');
            $table->boolean('is_visible')->default('f');
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
        Schema::drop('opinions');
    }

}
