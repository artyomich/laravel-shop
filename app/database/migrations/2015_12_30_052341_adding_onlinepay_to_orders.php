<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddingOnlinepayToOrders extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\Schema::table(
				'orders',
				function ($table) {
					$table->string('onlinepay', 30)->nullable();
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
				'orders', function ($table) {
			$table->dropColumn('onlinepay');
		});
	}

}
