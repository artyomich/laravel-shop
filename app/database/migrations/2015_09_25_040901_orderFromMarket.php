<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrderFromMarket extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table(
            'orders',
            /**
             * @var Blueprint $table
             */
            function (Blueprint $table) {
                $table->boolean('is_from_direct')->nullable();
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
            'orders',
            /**
             * @var Blueprint $table
             */
            function (Blueprint $table) {
                $table->dropColumn('is_from_direct');
            });
    }

}
