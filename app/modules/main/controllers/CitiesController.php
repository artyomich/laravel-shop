<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\main\controllers;

use models\Cities;

/**
 * Контроллер городов.
 */
class CitiesController extends \modules\main\components\BaseController
{
    /**
     * Главная страница со списком товаров.
     *
     * @return \Illuminate\View\View
     */
    public function postChange($id)
    {
        $city = Cities::find($id);
        $cityOld = Cities::find(\Cookie::get('city_id'));
        if (!$city) {
            return $this->answerAjax();
        }

        if (!$cityOld) {
            $cityOld = $city;
        }

        //  Если сейчас просматриваем что то связанное со старым погодом, то длаем редирект на новый.
        //  Для этого меняем alias старого города на новый.
        $url = str_replace($cityOld->alias, $city->alias, \URL::previous());

        //  Запишем в куки.
        \Cookie::queue('city_id', $city->id, 60 * 24 * 30);
        \Cookie::queue('city_alias', $city->alias, 60 * 24 * 30);

        return $this->redirectAjax($url);
    }
}
