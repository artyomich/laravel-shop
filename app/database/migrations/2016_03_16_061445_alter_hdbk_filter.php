<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterHdbkFilter extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table('hdbk_filter', function ($table) {
            $table->dropColumn('source');
        });
        \Schema::table('hdbk_filter', function ($table) {
            $table->text('source');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::table('hdbk_filter', function ($table) {
            $table->dropColumn('source');
        });
        \Schema::table('hdbk_filter', function ($table) {
            $table->string('source');
        });
    }

}
