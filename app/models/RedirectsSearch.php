<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

/**
 * Search модели Redirects
 *
 * @package models
 */
class RedirectsSearch extends Redirects
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
                    $model = $model->where($key, 'LIKE', '%' . $value . '%');
                }
            }
        }

        return $model->orderBy('id', 'desc');
    }
}