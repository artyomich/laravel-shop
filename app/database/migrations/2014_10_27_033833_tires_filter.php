<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TiresFilter extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'products_properties_relation', function (Blueprint $table) {
                $table->dropIndex('fk_products_has_properti00000');
                $table->dropIndex('products_properties_relation_product_id_index');
                \DB::statement(\DB::raw('ALTER TABLE products_properties_relation DROP CONSTRAINT "PRIMARY00010";'));
                $table->primary(['product_id', 'property_id', 'property_value']);
            }
        );

        Schema::create(
            'products_properties', function (Blueprint $table) {
                $table->increments('product_id');
                $table->string('brand')->length(32)->index();
                $table->string('manufacturer')->length(32);
                $table->string('model')->length(32);
                $table->string('size')->length(32);
                $table->string('diameter_inch')->length(32)->index();
                $table->string('diameter_mm')->length(32)->index();
                $table->string('width_inch')->length(32)->index();
                $table->string('width_mm')->length(32)->index();
                $table->string('series')->length(32)->index();
                $table->string('diameter_outside')->length(32);
                $table->string('layouts_normal')->length(32);
                $table->string('index_speed')->length(32);
                $table->string('index_load')->length(32);
                $table->string('season')->length(32)->index();
                $table->string('spikes')->length(32)->index();
                $table->string('image_axis')->length(64)->index();
                $table->string('camera')->length(64);
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
        Schema::drop('products_properties');
    }

}
