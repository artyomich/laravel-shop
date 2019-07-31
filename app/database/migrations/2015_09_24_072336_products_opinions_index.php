<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProductsOpinionsIndex extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products_opinions', function ($table) {
            $table->unique('market_opinion_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products_opinions', function ($table) {
            $table->dropUnique('products_opinions_market_opinion_id_unique');
        });
    }

}
