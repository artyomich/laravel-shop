<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BannersCriteria extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table(
            'banners', function (Blueprint $table) {
                $table->integer('city_id')->nullable();
                $table->integer('group_id')->nullable();
            }
        );

        \Schema::create(
            'banners_groups', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
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
            'banners', function (Blueprint $table) {
                $table->dropColumn('city_id');
                $table->dropColumn('group_id');
            }
        );

        \Schema::drop('banners_groups');
    }

}
