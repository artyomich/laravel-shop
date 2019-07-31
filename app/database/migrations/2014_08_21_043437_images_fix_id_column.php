<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ImagesFixIdColumn extends Migration
{

    /**
     * Run the migrations.
     */
    public function up()
    {
        \Schema::table(
            'images', function ($table) {
                $table->primary('id');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        \Schema::table(
            'images', function ($table) {
                $table->dropPrimary('id');
            }
        );
    }
}
