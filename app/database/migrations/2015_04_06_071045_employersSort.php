<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EmployersSort extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table(
            'employers',
            function (Blueprint $table) {
                $table->integer('sorting')->nullable();
            }
        );

        \DB::statement(\DB::raw('UPDATE employers SET sorting = id'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::table(
            'employers',
            function (Blueprint $table) {
                $table->dropColumn('sorting');
            }
        );
    }

}
