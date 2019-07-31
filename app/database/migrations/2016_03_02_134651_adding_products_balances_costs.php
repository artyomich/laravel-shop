<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddingProductsBalancesCosts extends Migration
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
            function ($table) {
                DB::statement("ALTER TABLE products_balances RENAME COLUMN cost1 TO cost_opt_small"); //Мелкий опт
                $table->integer('cost_opt_middle')->nullable(); //Средний опт
                $table->integer('cost_opt_big')->nullable(); //Крупный опт
                $table->integer('cost_spec')->nullable(); //Спец. предложение
                $table->integer('cost_min')->nullable(); //Минимальная цена
                $table->integer('cost_retail')->nullable(); //Розничная цена
                $table->integer('balance_full')->nullable(); //Полные остатки
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
            DB::statement("ALTER TABLE products_balances RENAME COLUMN cost_opt_small TO cost1");
            $table->dropColumn('cost_opt_middle');
            $table->dropColumn('cost_opt_big');
            $table->dropColumn('cost_spec');
            $table->dropColumn('cost_min');
            $table->dropColumn('cost_retail');
            $table->dropColumn('balance_full');
        });
    }

}
