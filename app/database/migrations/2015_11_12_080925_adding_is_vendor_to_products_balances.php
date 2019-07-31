<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddingIsVendorToProductsBalances extends Migration
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
                $table->integer('vendor_id')->nullable();
                $table->string('product_vendor_id', 100)->nullable();
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
            $table->dropColumn('vendor_id');
            $table->dropColumn('product_vendor_id');
        });
    }

}
