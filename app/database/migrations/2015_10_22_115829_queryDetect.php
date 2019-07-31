<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class QueryDetect extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table(
            'orders',
            /**
             * @var Blueprint $table
             */
            function (Blueprint $table) {
                $table->string('search_query', 100)->nullable();
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
            'orders',
            /**
             * @var Blueprint $table
             */
            function (Blueprint $table) {
                $table->dropColumn('search_query');
            });
    }

}
