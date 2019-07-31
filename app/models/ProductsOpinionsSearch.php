<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

/**
 * Search модели ProductsOpinions
 *
 * @package models
 */
class ProductsOpinionsSearch extends ProductsOpinions
{
    /**
     * @param array $params
     * @return ProductsOpinionsSearch
     */
    public static function search($params = [])
    {
        $model = new self;

        if (!empty($params[$model->formName()])) {
            foreach ($params[$model->formName()] as $key => $value) {
                if (!empty($value)) {
                    $model = $model->where($key, '=', $value);
                }
            }
        }

        return $model->orderBy('is_checked');
    }
}