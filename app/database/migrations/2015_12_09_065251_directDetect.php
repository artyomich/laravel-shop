<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DirectDetect extends Migration
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
                $table->string('direct_campaign')->nullable();
                $table->integer('direct_yclid')->nullable();
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
                $table->dropColumn('direct_campaign');
                $table->dropColumn('direct_yclid');
            }
        );
    }

}
