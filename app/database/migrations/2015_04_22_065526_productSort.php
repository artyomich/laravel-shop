<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProductSort extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table(
            'products',
            function (Blueprint $table) {
                $table->integer('sorting')->nullable();
                $table->index(['sorting']);
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
            'products',
            function (Blueprint $table) {
                $table->dropColumn('sorting');
                $table->dropIndex('sorting');
            }
        );
    }

}
