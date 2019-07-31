<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

use components\ActiveRecord;
use helpers\ArrayHelper;

/**
 * Search модели Products
 *
 * @package models
 */
class OrdersSearch extends Orders
{
    /**
     * Search.
     */
    public static function search($params = [])
    {
        $user = \Sentry::getUser();
        $importKey = \Input::get('key');
        $thisIs1C = !empty($importKey) && $importKey == \Config::get('app.importKey');

        if ($thisIs1C) {
            $isManager = false;
        } else {
            $groups = $user->getGroups();
            $isManager = $groups[0]->alias == 'managers';
        }

        /** @var ActiveRecord $model */
        $model = new self;
        $formName = $model->formName();
        $user = \Sentry::getUser();

        //  По статусу заказа.
        $_params = \Session::get('_OrderParams');
        $_params = json_decode(isset($_params) ? $_params : '{}');
        if (!$isManager) {
            $_params = [];
        }

        //  Суперпользователи могут видеть все заказы.
        //  Так же все заказы могут видеть и 1Сники при импорте.
        if ($thisIs1C || $user->hasAccess('admin.orders.viewAll')) {
            unset($_params->city_id);
        }

        //  Поиск по промежутку времени
        if (isset($params[$formName]['date_range'])) {
            if (!empty($params[$formName]['date_range'])) {
                $dates = explode('-', $params[$formName]['date_range']);
                $dates[0] = implode('-', array_reverse(explode('/', trim($dates[0]))));
                $dates[1] = implode('-', array_reverse(explode('/', trim($dates[1]))));
                $model = $model->whereBetween('date_create', $dates);
            }
            unset($params[$formName]['date_range']);
        }

        //  Поиск по номеру заказа или по ФИО заказчика.
        if (isset($params[$formName]['order'])) {
            if (!empty($params[$formName]['order'])) {
                $model = $model->where(
                    function ($query) use ($params, $formName) {
                        $query->where('user_name', 'like', '%' . $params[$formName]['order'] . '%');
                    }
                );
            }
            unset($params[$formName]['order']);
        }

        //  Дополнительная выборка.
        if (!empty($_params)) {
            foreach ($_params as $key => $value) {
                $model = $model->where($key, $value);
            }
        }

        if (!empty($params) && !empty($params[$formName])) {
            foreach ($params[$formName] as $key => $value) {
                if (!empty($value)) {
                    $model = $model->where($key, $value);
                }
            }
        }

        return $model->orderBy('id', 'desc');
    }
}