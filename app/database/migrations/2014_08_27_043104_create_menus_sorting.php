<?php
/**
 * Добавит поле сортирвки для таблицы меню.
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusSorting extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'menus', function (Blueprint $table) {
                $table->integer('sorting')->nullable();
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
        Schema::table(
            'menus', function ($table) {
                $table->dropColumn('sorting');
            }
        );
    }

}
