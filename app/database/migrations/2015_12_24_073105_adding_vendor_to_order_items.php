<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddingVendorToOrderItems extends Migration {

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
					$table->dropPrimary('order_id', 'product_id');
					$table->unsignedBigInteger('vendor_id')->nullable()->default(0);
				}
		);
		$orderItems = \models\OrderItems::get();
		foreach($orderItems as $item){
			$item->vendor_id = 0;
			$item->save();
		}
		\Schema::table(
				'order_items',
				function ($table) {
					$table->primary(['order_id', 'product_id', 'vendor_id']);
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
			$table->dropPrimary(['order_id', 'product_id', 'status']);
			$table->primary(['order_id', 'product_id']);
			$table->dropColumn('vendor_id');
		});
	}

}
