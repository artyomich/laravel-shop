<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersContracts extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::create(
            'users_contracts',
            function ($table) {
                $table->increments('id');
                $table->string('name', 100)->nullable();    //Наименование соглашения
                $table->integer('user_id')->nullable();     //ИД пользователя
                $table->integer('cost_type')->nullable();   //Тип цены
                $table->integer('delay_type')->nullable();  //Тип отсрочки
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
        \Schema::drop('users_contracts');
    }

}
