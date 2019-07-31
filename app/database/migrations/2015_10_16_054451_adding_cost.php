<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddingCost extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table(
            'products_balances',
            /**
             * @var  $table
             */
            function ($table) {
                $table->integer('cost1')->nullable();
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
            'products_balances', function ($table) {
            $table->dropColumn('cost1');
        }
        );
    }

}