<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Onlineconsult extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\Schema::create(
			'onlineconsult',
			function (Blueprint $table) {
				$table->integer('city_id')->unique();
				$table->string('city_key');
				$table->boolean('is_enable')->default('f');
			}
		);

		$data = [
			'Барнаул' => '556e738cfdec09c53861d274',
			'Белгород' => '556e76d1f81adc1b24228348',
			'Москва' => '556e778f5a5837c738086028',
			'Волгоград' => '556e789e5a5837c73808602a',
			'Челябинск' => '556e794a5a5837c73808602b',
			'Томск' => '556e79eb5a5837c73808602d',
			'Нижний Новгород' => '556e7aba5a5837c73808602f',
			'Красноярск' => '556e7c025a5837c738086031',
			'Екатеринбург' => '556e7c9bfdec09c53861d276',
			'Новосибирск' => '556e7d1b5a5837c738086034',
			'Ставрополь' => '556e7d885a5837c738086036',
			'Иркутск' => '556e7e03fdec09c53861d277',
			'Новокузнецк' => '556e7f6c5a5837c738086038'
		];

		foreach ($data as $cityname => $key) {
			$city = \models\Cities::where(['name' => $cityname])->first();
			$consult = new \models\OnlineConsult;
			$consult->city_id = $city->id;
			$consult->city_key = $key;
			$consult->save();
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		\Schema::drop('onlineconsult');
	}

}
