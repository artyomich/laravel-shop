<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Issue6 extends Migration
{

    /**
     * Run the migrations.
     *
     * @return bool
     */
    public function up()
    {
        /** @var \models\Categories[] $categories */
        $categories = \models\Categories::all();
        foreach ($categories as $category) {
            $category->type = \models\Categories::TYPE_TIRES;
            $category->save();
        }

        $category = new \models\Categories();
        $category->name = 'Диски легковые';
        $category->sorting = 6;
        $category->is_visible = true;
        $category->alias = 'legk-disk';
        $category->type = \models\Categories::TYPE_DISKS;
        if (!$category->save()) {
            return false;
        }

        $category = new \models\Categories();
        $category->name = 'Диски грузовые';
        $category->sorting = 7;
        $category->is_visible = true;
        $category->alias = 'gruz-disk';
        $category->type = \models\Categories::TYPE_DISKS;
        if (!$category->save()) {
            return false;
        }

        \Cache::forget('filter_disk_data');
        \Cache::rememberForever('filter_disk_data', function () {
            return [
                'ТипДиска' => [],
                'Сверловка' => [],
                'ДиаметрDIA' => [],
                'Диаметр' => [],
                'Вылет' => [],
                'Ширина' => [],
                'Бренд' => [],
            ];
        });

        return true;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /** @var \models\Categories[] $categories */
        $categories = \models\Categories::whereIn('alias', ['legk-disk', 'gruz-disk'])->get();
        foreach ($categories as $category) {
            $category->delete();
        }
    }

}
