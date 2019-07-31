<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ContractsDelays extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::create(
            'contracts_delays',
            function ($table) {
                $table->integer('max')->nullable();  //Максимальный срок отсрочки
                $table->integer('min')->nullable();  //Минимальный срок отсрочки
                $table->float('value')->nullable();  //Значение отсрочки
                $table->timestamps();
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
        \Schema::drop(
            'contracts_delays', function ($table) {
            $table->dropColumn('max');
            $table->dropColumn('min');
            $table->dropColumn('value');
            $table->dropTimestamps();
        });
    }
}