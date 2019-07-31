<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\main\widgets;

use components\Widget;
use models\Cities;
use models\Categories;

/**
 * FilterSimple widget.
 */
class FilterSimple extends Widget
{
    /**
     * @var array
     */
    public $input;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $catalogSorting = \Config::get('catalogSorting', []);
        $filterData = \Config::get('filter', []);
        $filter = \Config::get('filterDataByCategories', []);
        $filterDataDisk = \Config::get('filterDisk', []);

        $cityId = \Cookie::get('city_id', Cities::CITY_BY_DEFAULT);
        if (!isset($filterData[$cityId]) || !isset($filter[$cityId])) {
            return '';
        }

        $input = !empty($this->input) ? $this->input : \Input::all();
        if (!isset($input['Filter'])) {
            $input['Filter'] = [];
        }

        $filterData = $filterData[$cityId];

        //  Сортировка брендов.
        $catalogSorting = array_reverse($catalogSorting);
        foreach ($catalogSorting as $brand) {
            if (!isset($filterData['Бренд'][$brand])) {
                continue;
            }
            $temp = array($brand => $filterData['Бренд'][$brand]);
            unset($filterData['Бренд'][$brand]);
            $filterData['Бренд'] = $temp + $filterData['Бренд'];
        }

        ksort($filterData['Диаметр'], SORT_NUMERIC);
        ksort($filterData['Ширина'], SORT_NUMERIC);
        ksort($filterData['Высота'], SORT_NUMERIC);
        ksort($filterData['Рисунок']);

        //  Совместимость со старыми сылками.
        if (isset($input['Filter']['Ширина, мм'])) {
            $input['Filter']['Ширина'] = reset($input['Filter']['Ширина, мм']);
        } else if (isset($input['Filter']['Ширина, дюймы'])) {
            $input['Filter']['Ширина'] = reset($input['Filter']['Ширина, дюймы']);
        }

        if (isset($input['Filter']['Диаметр, мм'])) {
            $input['Filter']['Диаметр'] = reset($input['Filter']['Диаметр, мм']);
        } else if (isset($input['Filter']['Диаметр, дюймы'])) {
            $input['Filter']['Диаметр'] = reset($input['Filter']['Диаметр, дюймы']);
        }

        //  Заменим ключи.
        $filter = isset($filter[$cityId]) ? $filter[$cityId] : [];

        $alias = isset($this->_params['categoryAlias']) ? $this->_params['categoryAlias'] : '';
        $category = Categories::where('alias', $alias)->remember(120)->first();

        \View::addLocation(__DIR__ . '/views/filtersimple/');

        return $this->render(
            'index', [
                'input' => $input['Filter'],
                'filterData' => $filterData,
                'filterDataDisk' => $filterDataDisk,
                'filter' => $filter,
                'categories' => Categories::getAll(),
                'categoryAlias' => $alias,
                'category' => $category,
            ]
        );
    }
}