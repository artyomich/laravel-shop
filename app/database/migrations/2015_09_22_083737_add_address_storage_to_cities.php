<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAddressStorageToCities extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\Schema::table(
			'cities',
			/**
			 * @var  $table
			 */
			function ($table) {
				$table->string('address_storage', 64)->nullable();
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
			'cities', function ($table) {
			$table->dropColumn('address_storage');
		}
		);
	}

}
