<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

/**
 * Search модели Pages
 *
 * @package models
 */
class PagesSearch extends Pages
{
    /**
     * Search.
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

        return $model->orderBy('date_create', 'desc');
    }
}