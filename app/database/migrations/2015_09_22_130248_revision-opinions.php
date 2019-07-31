<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RevisionOpinions extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table(
            'products_opinions',
            /**
             * @var  $table
             */
            function ($table) {
                $table->integer('market_model_id')->nullable();
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
            'products_opinions', function ($table) {
            $table->dropColumn('market_model_id');
        });
    }

}
