<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexTo1cColumnInProductsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'products', function ($table) {
                $table->index('id_1c');
            }
        );

        Schema::table(
            'products_properties_relation', function ($table) {
                $table->index('product_id');
            }
        );

        /*Schema::table(
            'products_balances', function ($table) {
                $table->primary(['product_id', 'city_id']);
            }
        );*/

        /*Schema::table(
            'products_balances', function ($table) {
                $table->primary(['product_id', 'city_id']);
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
        Schema::table(
            'products', function ($table) {
                $table->dropIndex('products_id_1c_index');
            }
        );

        Schema::table(
            'products_properties_relation', function ($table) {
                $table->dropIndex('products_properties_relation_product_id_index');
            }
        );

        /*Schema::table(
            'products_balances', function ($table) {
                $table->dropPrimary(['product_id', 'city_id']);
            }
        );*/
    }

}
