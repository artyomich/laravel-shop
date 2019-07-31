<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\main\controllers;

use helpers\ArrayHelper;
use models\Products;
use models\ProductsBalances;
use models\ProductsSearch;
use modules\main\widgets\Categories;

/**
 * Контроллер фильтров.
 */
class FilterController extends \modules\main\components\BaseController
{
    /**
     * Фильтр шин.
     * Отправляются только для выбранного города.
     */
    public function postTires()
    {
        $cityId = \Cookie::get('city_id', 1);
        $filter = require(app_path() . '/config/filter.php');

        return $this->answerAjax(urldecode(json_encode($filter[$cityId])));
    }

    /**
     * Отправит количество шин по выборке.
     */
    public function postCount($categoryId = null)
    {
        $input = \Input::all();
        if (!isset($input['Filter'])) {
            $input['Filter'] = [];
        }

        if (!empty($categoryId)) {
            $input['Filter']['category_id'] = $categoryId;
        }

        return $this->answerAjax(json_encode([
            'count' => ProductsSearch::filter($input)->count()
        ]));
    }

    /**
     * Отправит список доступных брендов у категории.
     * @param integer $categoryId
     * @return string
     */
    public function postBrands($categoryId)
    {
        $cityId = \Cookie::get('city_id', 1);
        $categoryId = (int)$categoryId;

        $brands = ProductsBalances
            ::join('products_properties', 'products_properties.product_id', '=', 'products_balances.product_id')
            ->join('products', 'products.id', '=', 'products_properties.product_id')
            ->where('products_balances.balance', '>', 0)
            ->where('products_balances.cost', '>', 0)
            ->where('products_balances.city_id', '=', $cityId)
            ->where('products.is_visible', '=', 't')
            ->groupBy('products_properties.brand')
            ->select('products_properties.brand');

        if (!empty($categoryId)) {
            $brands->where('products.category_id', '=', $categoryId);
        }

        return $this->answerAjax(json_encode(array_values(ArrayHelper::map($brands->get(), 'brand', 'brand'))));
    }
}