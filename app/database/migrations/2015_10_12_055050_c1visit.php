<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class C1visit extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::create(
            'visit_1c',
            /**
             * @var Blueprint $table
             */
            function (Blueprint $table) {
                $table->increments('id');
                $table->dateTime('date_create');
                $table->dateTime('date_update');
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
        \Schema::drop('visit_1c');
    }

}
