<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Hdbkfilter extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::create(
            'hdbk_filter',
            function ($table) {
                $table->increments('id');
                $table->string('alias');
                $table->string('source');
                $table->text('description')->nullable();
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
        \Schema::drop('hdbk_filter');
    }

}
