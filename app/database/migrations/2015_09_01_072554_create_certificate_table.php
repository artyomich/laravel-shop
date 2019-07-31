<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCertificateTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::create('certificates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('brand', 100);
            $table->string('img', 100)->nullable();
            $table->text('desc')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::drop('certificates');
    }

}
