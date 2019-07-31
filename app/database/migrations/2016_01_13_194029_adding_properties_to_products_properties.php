<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddingPropertiesToProductsProperties extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table(
            'products_properties',
            function ($table) {
                $table->string('type', 32)->nullable(); //тип (шины, диск)
                $table->string('offset', 32)->nullable(); //вылет
                $table->string('drilling', 32)->nullable(); //сверловка
                $table->string('construction', 32)->nullable();//тип (литой, кованный)
                $table->string('diameter_inside', 32)->nullable(); //диаметр центрального отверстия
                $table->string('bolt_pattern', 32)->nullable(); //количество крепежных отверстий
            }
        );
        \Schema::table(
            'categories',
            function ($table) {
                $table->tinyInteger('type')->nullable(); //тип (шины, диск)
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
            'products_properties', function ($table) {
            $table->dropColumn('type');
            $table->dropColumn('offset');
            $table->dropColumn('drilling');
            $table->dropColumn('construction');
            $table->dropColumn('diameter_inside');
            $table->dropColumn('bolt_pattern');
        });
        \Schema::table(
            'categories', function ($table) {
            $table->dropColumn('type');
        });
    }

}
