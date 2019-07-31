<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePropertiesOfTheTableOrders extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\Schema::table(
			'orders', function ($table) {
			DB::statement("ALTER TABLE orders ALTER COLUMN delivery_cost DROP NOT NULL");
			DB::statement("ALTER TABLE orders ALTER COLUMN delivery_city_id DROP NOT NULL");
		});
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
			DB::statement("ALTER TABLE orders ALTER COLUMN delivery_cost SET NOT NULL");
			DB::statement("ALTER TABLE orders ALTER COLUMN delivery_city_id SET NOT NULL");
		});
	}

}
