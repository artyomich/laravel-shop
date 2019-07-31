<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

/**
 * Search модели Opinions
 *
 * @package models
 */
class OpinionsSearch extends Opinions
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

        return $model;
    }
}