<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePropertiesOfTheTableProductsBalanses extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table(
            'products_balances', function ($table) {
            DB::statement("ALTER TABLE products_balances ALTER COLUMN city_id DROP NOT NULL");
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
            'products_balances', function ($table) {
            DB::statement("ALTER TABLE products_balances ALTER COLUMN city_id SET NOT NULL");
        });
    }

}
