<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Hdbk404 extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::drop('hdbk404');
        \Schema::create(
            'log_errors',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('url');
                $table->string('method');
                $table->integer('code')->length(3)->nullable();
                $table->string('referer')->nullable();
                $table->string('user_agent');
                $table->string('remote_url')->nullable();
                $table->string('date_create');
                $table->string('date_update');
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
        \Schema::drop('log_errors');
        \Schema::create(
            'hdbk404',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('url');
                $table->string('method');
                $table->string('referer')->nullable();
                $table->string('user_agent');
                $table->string('remote_url')->nullable();
                $table->string('date_create');
                $table->string('date_update');
            }
        );
    }

}
