<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisibleFieldPages extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'pages', function (Blueprint $table) {
                $table->boolean('is_visible')->default('FALSE')->nullable();
                $table->dropColumn('meta_name');
                $table->string('meta_title')->length(64)->nullable();
                $table->dropColumn('id');
            }
        );
        Schema::table(
            'pages', function (Blueprint $table) {
                $table->increments('id');
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
            'pages', function (Blueprint $table) {
                $table->dropColumn('is_visible');
                $table->dropColumn('meta_title');
                $table->string('meta_name')->length(64)->nullable();
                $table->dropColumn('id');
            }
        );
        Schema::table(
            'pages', function (Blueprint $table) {
                $table->bigInteger('id')->nullable();
            }
        );
    }

}
