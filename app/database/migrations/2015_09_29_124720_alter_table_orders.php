<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableOrders extends Migration
{

    public function up()
    {
        DB::statement('ALTER TABLE orders  ALTER COLUMN is_from_direct TYPE  CHAR(1)');
        DB::statement("UPDATE orders SET is_from_direct='D' WHERE is_from_direct='1'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }

}
