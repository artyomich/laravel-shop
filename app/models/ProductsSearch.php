<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

use components\ActiveRecord;
use helpers\ArrayHelper;
use models\ProductsBalances;

/**
 * Search модели Products
 *
 * @package models
 */
class ProductsSearch extends Products
{
    /**
     * @param array $params
     * @return ProductsSearch
     */
    public static function search($params = [])
    {
        $model = new self;
        $likeColList = ['id_1c', 'name'];

        if (\Input::get('showDoubles')) {
            return self::doubles($params);
        }

        if (!empty($params[$model->formName()])) {
            foreach ($params[$model->formName()] as $key => $value) {
                if (!empty($value)) {
                    $model = in_array($key, $likeColList)
                        ? $model->where($key, 'like', '%' . $value . '%')
                        : $model->where($key, $value);
                }
            }
        }

        return $model;
    }


    public static function getCityProductsIdWithVendors($city){
        return \Cache::remember('getCityProductsWithVendors.'.$city, 120, function() use($city){
            return \DB::select('select array_to_string( array (select distinct id from products
                inner join products_balances on products_balances.product_id = products.id
                where is_visible = true
                and products_balances.cost > 0
                and  (products_balances.city_id = ' . $city  . "
                or (products_balances.city_id is null and products_balances.vendor_id is not null))),',')")[0]->array_to_string;
        });
    }

    /**
     * Выборка для фильтра шин.
     *
     * @param array $params
     */
    public static function filter($params = [])
    {
        $likeColList = ['name'];
        $colList = ['category_id'];
        $params = is_array($params['Filter']) ? $params['Filter'] : [];

        $filterMap = [
            //  Шины
            'Диаметр, дюймы' => 'diameter_inch',
            'Диаметр, мм' => 'diameter_mm',
            'Диаметр' => ['diameter_inch', 'diameter_mm'],
            'Ширина, дюймы' => 'width_inch',
            'Ширина, мм' => 'width_mm',
            'Ширина' => ['width_inch', 'width_mm'],
            'Высота' => 'series',
            'Бренд' => 'brand',
            'Модель' => 'model',
            'Рисунок' => 'image_axis',
            'Сезон' => 'season',

            //  Диски
            'Вылет' => 'offset',
            'Сверловка' => 'drilling',
            'ТипДиска' => 'construction',
            'ДиаметрDIA' => 'diameter_inside',
            'Количество крепежных отверстий' => 'bolt_pattern'
        ];

        //  Сортировка списка товаров по брендам, часть из которых должна быть всегда на первом месте.
        //  FIXME: Использовать \Config('...');
        $catalogSorting = require(app_path() . '/config/catalogSorting.php');
        $orderBy = [];
        foreach ($catalogSorting as $k => $sort) {
            $orderBy[] = "WHEN brand='" . $sort . "' THEN " . $k;
        }
        $selectFields = array_merge(['*'], ProductsBalances::getCostFields());
        if (isset(\Sentry::getUser()->is_firm) and \Sentry::getUser()->is_firm) {
            $model = self::whereRaw('id in ('.static::getCityProductsIdWithVendors(Cities::getCurrentCity()->id).')')
                ->join('products_properties', 'products_properties.product_id', '=', 'products.id');
        } else {
            $model = self::join('products_balances', 'products_balances.product_id', '=', 'products.id')
                ->join('products_properties', 'products_properties.product_id', '=', 'products.id')
                ->where('is_visible', true)
                ->where('balance','>', 0)
                ->where('balance_full','>', 0)
                ->where('products_balances.cost', '>', 0)
                ->where('products_balances.city_id', '=', Cities::getCurrentCity()->id)
                ->select($selectFields);
        }
        foreach ($params as $name => $param) {
            //  Нули не обрабатываем
            if (empty($param)) {
                continue;
            }

            if (!is_array($param)) {
                $param = [$param];
            }

            if ($name == 'Шипы') {
                $model = $model->where('products_properties.spikes', 'Да');
                continue;
            }

            if ($name == 'СортировкаЦена') {
                $model = $model->orderByRaw('products_balances.cost ' . ($param[0] == 'DESC' ? 'DESC' : 'ASC'));
                continue;
            }

            if (isset($filterMap[$name])) {

                if (array_key_exists($name, $filterMap)) {
                    if ($name == 'Рисунок' && in_array('Все', $params['Рисунок'])) {
                        continue;
                    }

                    $model = $model
                        ->where(
                            function ($query) use ($filterMap, $name, $param) {
                                foreach ($param as $item) {
                                    if (empty($item)) {
                                        continue;
                                    }

                                    //  Это для упрощунного фильтра. Поиск по двум полям сразу: дюймы и мм.
                                    if (is_array($filterMap[$name])) {
                                        $query->orWhere(
                                            function ($query) use ($filterMap, $name, $item) {
                                                foreach ($filterMap[$name] as $k => $mi) {
                                                    $query = $query->orWhere('products_properties.' . $filterMap[$name][$k], trim($item));
                                                }

                                            }
                                        );
                                    } else {
                                        $query->orWhere('products_properties.' . $filterMap[$name], trim($item));
                                    }
                                }
                            }
                        );

                    continue;
                }
            }
            if (in_array($name, $likeColList)) {
                $model = $model->whereRaw('toUpperCase(' . $name . ') ILIKE toUpperCase(?)', ['%' . trim(implode('%', $param)) . '%']);
            }
            if (in_array($name, $colList)) {
                $model = $model->where($name, trim(implode(' ', $param)));
            }

        }
        return $model;
    }

    /**
     * Выборка для фильтра дисков.
     *
     * @param array $params
     */
    /*public static function filterDisk($params = [])
    {
        $likeColList = ['name'];
        $params = is_array($params['Filter']) ? $params['Filter'] : [];

        $filterMap = [
        ];

        //  Сортировка списка товаров по брендам, часть из которых должна быть всегда на первом месте.
        //  FIXME: Использовать \Config('...');
        $selectFields = array_merge(['*'], ProductsBalances::getCostFields());
        $model = self::join('products_balances', 'products_balances.product_id', '=', 'products.id')
            ->join('products_properties', 'products_properties.product_id', '=', 'products.id')
            ->where('is_visible', true)
            ->where('products_balances.cost', '>', 0)
            ->where('products_balances.city_id', '=', \Cookie::get('city_id'))
            ->select($selectFields);
        foreach ($params as $name => $param) {
            //  Нули не обрабатываем
            if (empty($param)) {
                continue;
            }

            if (!is_array($param)) {
                $param = [$param];
            }

            if ($name == 'СортировкаЦена') {
                $model = $model->orderByRaw('products_balances.cost ' . ($param[0] == 'DESC' ? 'DESC' : 'ASC'));
                continue;
            }


            if (isset($filterMap[$name])) {
                if (array_key_exists($name, $filterMap)) {
                    if ($name == 'Рисунок' && in_array('Все', $params['Рисунок'])) {
                        continue;
                    }

                    $model = $model
                        ->where(
                            function ($query) use ($filterMap, $name, $param) {
                                foreach ($param as $item) {
                                    if (empty($item)) {
                                        continue;
                                    }

                                    //  Это для упрощунного фильтра. Поиск по двум полям сразу: дюймы и мм.
                                    if (is_array($filterMap[$name])) {
                                        $query->orWhere(
                                            function ($query) use ($filterMap, $name, $item) {
                                                foreach ($filterMap[$name] as $k => $mi) {
                                                    $query = $query->orWhere('products_properties.' . $filterMap[$name][$k], trim($item));
                                                }

                                            }
                                        );
                                    } else {
                                        $query->orWhere('products_properties.' . $filterMap[$name], trim($item));
                                    }
                                }
                            }
                        );

                    continue;
                }
            }
            $model = in_array($name, $likeColList)
                ? $model->whereRaw('toUpperCase(' . $name . ') ILIKE toUpperCase(\'%' . trim(implode('%', $param)) . '%\')')
                : $model->where($name, trim(implode(' ', $param)));
        }

        return $model;
    }*/

    /**
     * Поиск дублей товаров.
     * @param array $params
     */
    public static function doubles($params = [])
    {
        $properties = \DB::select(\DB::raw("WITH summary AS (
                SELECT p.product_id,
                       p.model,
                       p.model,
                       p.size,
                       p.diameter_inch,
                       p.diameter_mm,
                       p.width_inch,
                       p.width_mm,
                       p.series,
                   p.diameter_outside,
                   p.layouts_normal,
                   p.index_speed,
                   p.index_load,
                   p.season,
                   p.spikes,
                   p.image_axis,
                   p.camera,
                   p.completeness,
                       ROW_NUMBER() OVER(PARTITION BY p.model, p.model, p.size, p.diameter_inch,
                           p.diameter_mm, p.width_inch, p.width_mm, p.series,
                           p.diameter_outside, p.layouts_normal, p.index_speed,
                           p.index_load, p.season, p.spikes, p.image_axis,
                           p.camera, p.completeness) AS rk
                  FROM products_properties p)
            SELECT s.product_id
            FROM summary s
            WHERE s.rk > 1"));
        $ids = [];
        foreach ($properties as $property) {
            $ids[] = $property->product_id;
        }

        return self::join('products_properties', 'products_properties.product_id', '=', 'products.id')
            ->whereIn('product_id', $ids);
    }
}