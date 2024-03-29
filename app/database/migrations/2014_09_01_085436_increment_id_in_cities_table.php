<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IncrementIdInCitiesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table(
            'cities', function ($table) {
                $table->dropColumn('id');
            }
        );
        \Schema::table(
            'cities', function ($table) {
                $table->increments('id');
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
        \Schema::table(
            'cities', function ($table) {
                $table->dropColumn('id');
            }
        );
        \Schema::table(
            'cities', function ($table) {
                $table->integer('id');
            }
        );
    }

}
