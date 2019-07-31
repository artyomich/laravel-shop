<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterOrdersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table(
            'orders', function ($table) {
            $table->integer('delivery_cost')->default(0);
            $table->integer('delivery_city_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::table(
            'orders', function ($table) {
            $table->dropColumn('delivery_cost');
            $table->dropColumn('delivery_city_id');
        });
    }

}
