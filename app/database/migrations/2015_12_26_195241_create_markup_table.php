<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarkupTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\Schema::create('markup', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name',50)->nullable();
			$table->integer('value')->nullable();
		});
		$markup=['name'=>'Общая наценка','value'=>5];
		DB::table('markup')->insert($markup);	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		\Schema::drop('markup');
	}

}
