<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MenusFixIdColumn extends Migration
{

    /**
     * Run the migrations.
     */
    public function up()
    {
        if (\Schema::hasColumn('menus', 'id')) {
            \Schema::table(
                'menus', function ($table) {
                    $table->dropColumn('id');
                }
            );
        }

        if (\Schema::hasColumn('menus', 'menu_id')) {
            \Schema::table(
                'menus', function ($table) {
                    $table->dropColumn('menu_id');
                }
            );
        }

        \Schema::table(
            'menus', function ($table) {
                $table->increments('id');
                $table->integer('menu_id')->nullable();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (\Schema::hasColumn('menus', 'id')) {
            \Schema::table(
                'menus', function ($table) {
                    $table->dropColumn('id');
                }
            );
        }

        \Schema::table(
            'menus', function ($table) {
                $table->bigInteger('id')->nullable()->before('name');
            }
        );
    }
}
