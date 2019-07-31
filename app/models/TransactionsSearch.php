<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

use components\ActiveRecord;
use helpers\ArrayHelper;

/**
 * Search модели Transactions
 *
 * @package models
 */
class TransactionsSearch extends Transactions
{
	/**
	 * Search.
	 *
	 * @param array $params
	 */
	public static function search($params = [])
	{
		$model = new self;

		if (!empty($params[$model->formName()])) {
			foreach ($params[$model->formName()] as $key => $value) {
				if (!empty($value)) {
					$model = $model->where($key, $value);
				}
			}
		}

		return $model;
	}

	/**
	 * Выборка для фильтра.
	 *
	 * @param array $params
	 */
	public static function filter($params = [])
	{
		$likeColList = ['name'];
		$params = is_array($params['Filter']) ? $params['Filter'] : [];

		$filterMap = [
			'Диаметр, дюймы' => 'diameter_inch',
			'Диаметр, мм' => 'diameter_mm',
			'Диаметр' => ['diameter_inch', 'diameter_mm'],
			'Ширина, дюймы' => 'width_inch',
			'Ширина, мм' => 'width_mm',
			'Ширина' => ['width_inch', 'width_mm'],
			'Высота' => 'series',
			'Бренд' => 'brand',
			'Рисунок' => 'image_axis',
			'Сезон' => 'season'
		];

		//  Сортировка списка товаров по брендам, часть из которых должна быть всегда на первом месте.
		$catalogSorting = require(app_path() . '/config/catalogSorting.php');
		$orderBy = [];
		foreach ($catalogSorting as $k => $sort) {
			$orderBy[] = "WHEN brand='" . $sort . "' THEN " . $k;
		}

		$model = self::join('products_balances', 'products_balances.product_id', '=', 'products.id')
			->join('products_properties', 'products_properties.product_id', '=', 'products.id')
			->where('is_visible', true)
			->where('products_balances.cost', '>', 0)
			->where('products_balances.city_id', '=', \Cookie::get('city_id'))
			->orderByRaw("CASE " . implode(' ', $orderBy) . " ELSE " . (count($orderBy) + 1) . " END");

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
				? $model->where($name, 'ILIKE', '%' . trim(implode('%', $param)) . '%')
				: $model->where($name, trim(implode(' ', $param)));
		}

		return $model;
	}

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