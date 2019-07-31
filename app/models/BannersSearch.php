<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

use Illuminate\Database\Query\Builder;

/**
 * Search модели Banners
 *
 * @package models
 */
class BannersSearch extends Banners
{
    /**
     * @param array $params
     * @return BannersSearch
     */
    public static function search($params = [])
    {
        $model = new self;

        if (!empty($params[$model->formName()])) {
            foreach ($params[$model->formName()] as $key => $value) {
                if (!empty($value)) {
                    /** @var Builder $model */
                    $model = $model->where($key, '=', $value);
                }
            }
        }

        return $model;
    }
}