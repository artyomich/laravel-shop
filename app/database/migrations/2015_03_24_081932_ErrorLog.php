<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ErrorLog extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table(
            'log_errors',
            function (Blueprint $table) {
                $table->text('message')->nullable();
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
            'log_errors',
            function (Blueprint $table) {
                $table->dropColumn('message');
            }
        );
    }

}
