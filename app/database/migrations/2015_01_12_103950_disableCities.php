<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DisableCities extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table(
            'cities',
            function (Blueprint $table) {
                $table->boolean('is_visible')->default('t')->nullable();
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
            'cities',
            function (Blueprint $table) {
                $table->dropColumn('is_visible');
            }
        );
    }

}
