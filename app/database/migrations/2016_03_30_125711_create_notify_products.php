<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotifyProducts extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\Schema::create(
			'products_notifications', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('product_id');
				$table->integer('city_id')->nullable();
				$table->string('email',32)->nullable();
				$table->string('phone',16)->nullable();
				$table->datetime('expiration');
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
		\Schema::drop('products_notifications');
	}

}
