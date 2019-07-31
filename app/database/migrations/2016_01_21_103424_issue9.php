<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Issue9 extends Migration
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
            function (Blueprint $table) {
                $table->boolean('is_spec_cost')->default('f');
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
            'products_balances',
            function (Blueprint $table) {
                $table->dropColumn('is_spec_cost');
            }
        );
    }

}
