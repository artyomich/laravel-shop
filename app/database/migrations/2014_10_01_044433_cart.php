<?php
/**
 * Ветка banners.
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class Cart extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement(\DB::raw("ALTER TABLE pages ALTER COLUMN description DROP NOT NULL;"));
        /*Schema::table(
            'pages', function (Blueprint $table) {
                $table->nullable('description');
            }
        );*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

}
