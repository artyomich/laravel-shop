<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixOpinionsColumn extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(DB::raw('ALTER TABLE products_opinions ALTER COLUMN market_opinion_id DROP NOT NULL'));
        DB::statement(DB::raw('ALTER TABLE products_opinions ALTER COLUMN user_advantages DROP NOT NULL'));
        DB::statement(DB::raw('ALTER TABLE products_opinions ALTER COLUMN user_disadvantages DROP NOT NULL'));
        DB::statement(DB::raw('ALTER TABLE products_opinions ALTER COLUMN user_comment DROP NOT NULL'));

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
