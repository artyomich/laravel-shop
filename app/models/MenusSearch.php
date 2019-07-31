<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

/**
 * Search модели Menus
 *
 * @package models
 */
class MenusSearch extends Menus
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

        return $model->orderBy('parent_id', 'desc')->orderBy('sorting');
    }
}