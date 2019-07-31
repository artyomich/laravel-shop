<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Productopinions extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::create('products_opinions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id');
            $table->integer('market_opinion_id', false, true);
            $table->string('user_fullname')->nullable();
            $table->text('user_advantages');
            $table->text('user_disadvantages');
            $table->text('user_comment');
            $table->smallInteger('rating', false, true);
            $table->dateTime('date_create');
            $table->boolean('is_checked')->default('f');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::drop('products_opinions');
    }

}
