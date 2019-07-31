<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddingDeliveryToOrderItems extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\Schema::table(
			'order_items',
			function ($table) {
				$table->integer('delivery')->nullable();
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
			'order_items', function ($table) {
			$table->dropColumn('delivery');
		});
	}

}
