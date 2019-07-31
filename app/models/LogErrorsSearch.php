<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

/**
 * Search модели Products
 *
 * @package models
 */
class LogErrorsSearch extends LogErrors
{
    /**
     * Search.
     */
    public static function search($params = [])
    {
        $model = new self;
        $likeColList = [];

        if (!empty($params[$model->formName()])) {
            foreach ($params[$model->formName()] as $key => $value) {
                if (!empty($value)) {
                    $model = in_array($key, $likeColList)
                        ? $model->where($key, 'like', '%' . $value . '%')
                        : $model->where($key, $value);
                }
            }
        }

        return $model->orderBy('date_create', 'desc');
    }
}