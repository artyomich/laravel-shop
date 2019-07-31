<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\main\widgets;

use components\Widget;
use models\Cities;
use modules\main\components\BaseController;

/**
 * Banners widget.
 */
class Filter extends Widget
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $viewName = $this->categoryAlias != 'legk' ? 'gruz' : 'index';
        $catalogSorting = require(app_path() . '/config/catalogSorting.php');
        $filter = require(app_path() . '/config/filter.php');
        $filterByCategory = require(app_path() . '/config/filterDataByCategories.php');
        $cityId = \Cookie::get('city_id', Cities::CITY_BY_DEFAULT);
        if (!isset($filter[$cityId]) || !isset($filterByCategory[$cityId])) {
            return '';
        }

        if ($this->categoryAlias) {
            $category = \models\Categories::getByAlias($this->categoryAlias);
        }

        $filter = $filter[$cityId];

        //  Сортировка брендов.
        $catalogSorting = array_reverse($catalogSorting);
        foreach ($catalogSorting as $brand) {
            if (!isset($filter['Бренд'][$brand])) {
                continue;
            }
            $temp = array($brand => $filter['Бренд'][$brand]);
            unset($filter['Бренд'][$brand]);
            $filter['Бренд'] = $temp + $filter['Бренд'];
        }

        ksort($filter['Диаметр']['дюймы'], SORT_NUMERIC);
        ksort($filter['Диаметр']['мм'], SORT_NUMERIC);
        ksort($filter['Ширина']['дюймы'], SORT_NUMERIC);
        ksort($filter['Ширина']['мм'], SORT_NUMERIC);
        ksort($filter['Высота'], SORT_NUMERIC);
        //ksort($filter['Бренд']);
        ksort($filter['Рисунок']);

        $input = \Input::all();
        if (!isset($input['Filter'])) {
            $input['Filter'] = [];
        }

        return $this->render(
            $viewName, [
                'input'            => $input['Filter'],
                'filter'           => $filter,
                'filterByCategory' => $this->categoryAlias && isset($filterByCategory[$cityId][$category->id])
                    ? $filterByCategory[$cityId][$category->id] : [],
                'categoryAlias'    => $this->categoryAlias
            ]
        );
    }
}