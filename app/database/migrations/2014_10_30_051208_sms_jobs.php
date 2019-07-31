<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SmsJobs extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'sms_jobs',
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('phone');
                $table->text('message');
                $table->string('accepted_time');
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
        Schema::drop('sms_jobs');
    }

}
