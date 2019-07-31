<?php
/**
 * Создание новой тоблицы стоимостей и остатков шин в разных городах.
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCostsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::create(
            'products_balances', function ($table) {
                $table->integer('product_id')->unsigned()->index();
                $table->integer('city_id')->unsigned()->index();
                $table->integer('cost')->nullable();
                $table->integer('balance')->nullable();
            }
        );

        /*Schema::table(
            'products_balances', function ($table) {
                //$table->foreign('product_id')->references('id')->on('products');
                //$table->foreign('city_id')->references('id')->on('cities');
                $table->index('product_id');
                $table->index('city_id');
            }
        );*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::drop('products_balances');
    }

}
