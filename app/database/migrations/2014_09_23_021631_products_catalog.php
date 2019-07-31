<?php
/**
 * Ветка products_catalog (Каталог товаров (фронтэнд))
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Ivanlemeshev\Laravel4CyrillicSlug\Facades\Slug;

class ProductsCatalog extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $categories = \models\Categories::all();

        Schema::table(
            'categories', function (Blueprint $t) {
                $t->string('alias')->after('name')->length(64)->default('');
            }
        );

        foreach ($categories as $category) {
            $category->alias = Slug::make($category->name, '_');
            $category->save();
        }

        Schema::table(
            'categories', function (Blueprint $t) {
                $t->unique('alias');
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
        Schema::table(
            'categories', function (Blueprint $t) {
                $t->dropUnique('categories_alias_unique');
                $t->dropColumn('alias');
            }
        );
    }

}
