<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCitiesTable extends Migration
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
                $table->boolean('online_pay_delivery')->default('f')->nullable();
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
                $table->dropColumn('online_pay_delivery');
            }
        );
    }

}
