<?php
/**
 * Ветка banners.
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class Banners extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'banners', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('image_id')->unsigned()->index();
                $table->string('name');
                $table->string('link');
                $table->integer('sorting')->unsigned();
                $table->boolean('is_visible')->default('f');
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
        Schema::drop('banners');
    }

}
