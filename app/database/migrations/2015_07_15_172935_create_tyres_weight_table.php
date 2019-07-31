<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTyresWeightTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        \Schema::create('tyres_weight', function(Blueprint $table) {
            $table->integer('width');
            $table->integer('profile');
            $table->decimal('diameter', 10, 2);
            $table->decimal('volume', 10, 2);
            $table->decimal('weight', 10, 2);
        });
        $tyres = \helpers\CsvToArray::toArray(app_path() . '/database/seeds/tyres_weight.csv', ';');
        foreach ($tyres as $tyre)
            DB::table('tyres_weight')->insert($tyre);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        \Schema::drop('tyres_weight');
    }

}
