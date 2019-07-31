<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Fixcolumndirectdetect extends Migration
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
            function ($table) {
                $table->dropColumn('direct_yclid');
                $table->string('direct_ad_id')->nullable();
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
            function ($table) {
                $table->dropColumn('direct_ad_id');
                $table->integer('direct_yclid')->nullable();
            }
        );
    }

}
