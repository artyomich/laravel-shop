<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SmsNotice extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'cities',
            function (Blueprint $table) {
                $table->smallInteger('work_begin')->nullable();
                $table->smallInteger('work_end')->nullable();
                $table->string('email_manager')->nullable();
                $table->string('phone_manager')->nullable();
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
        Schema::table(
            'cities',
            function (Blueprint $table) {
                $table->dropColumn('work_begin');
                $table->dropColumn('work_end');
                $table->dropColumn('email_manager');
                $table->dropColumn('phone_manager');
            }
        );
    }

}
