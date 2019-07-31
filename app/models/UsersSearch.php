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
class UsersSearch extends Users
{
    /**
     * Search.
     */
    public static function search($params = [])
    {
        $model = new self;
        $likeColList = ['email', 'first_name', 'last_name'];
        if (!empty($params[$model->formName()])) {
            foreach ($params[$model->formName()] as $key => $value) {
                if ($key === 'group_id') {
                    $model = $model->whereIn('id', \models\UsersGroupsRelation::where('group_id', $value)->select('user_id')->lists('user_id'));
                } elseif (!empty($value)) {
                    $model = in_array($key, $likeColList)
                        ? $model->where($key, 'ilike', '%' . $value . '%')
                        : $model->where($key, $value);
                }
            }
        }

        return $model->orderBy('created_at', 'desc');
    }


}