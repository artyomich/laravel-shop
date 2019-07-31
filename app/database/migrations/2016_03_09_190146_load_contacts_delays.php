<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LoadContactsDelays extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $delays = [
            ['min' => 0, 'max' => 7, 'value' => 1],
            ['min' => 8, 'max' => 14, 'value' => 1.0075],
            ['min' => 15, 'max' => 21, 'value' => 1.01],
            ['min' => 22, 'max' => 30, 'value' => 1.015],
            ['min' => 31, 'max' => 45, 'value' => 1.0225],
            ['min' => 46, 'max' => 60, 'value' => 1.03],
            ['min' => 61, 'max' => 90, 'value' => 1.045],
        ];
        foreach ($delays as $delay) {

            $model = new \models\ContractsDelays();
            $model->min = $delay['min'];
            $model->max = $delay['max'];
            $model->value = $delay['value'];
            if (!$model->save()) {
                throw new Exception('Не удалось сохранить модель');
            }
        }
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

}
