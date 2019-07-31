<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddArticlesCategoriesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::create(
            'pages_categories', function ($table) {
                $table->increments('id');
                $table->string('name')->length(64);
                $table->string('alias')->length(64);
                $table->integer('sorting');
                $table->boolean('is_visible')->default('FALSE');
            }
        );

        \Schema::table(
            'pages', function ($table) {
                $table->integer('category_id')->nullable();
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
        \Schema::drop('pages_categories');

        \Schema::table(
            'pages', function ($table) {
                $table->dropColumn('category_id');
            }
        );
    }

}
