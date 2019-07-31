<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\main\components;

use components\ActiveRecord;
use components\SxGeo;
use components\yandex\YandexPartnerApi;
use models\Cities;
use models\Pages;

/**
 * Базовый контроллер для панели администрирования.
 *
 * @property ActiveRecord $city
 * @property ActiveRecord $page
 * @property string $title
 */
class BaseController extends \components\BaseController
{
    /**
     * @const string город по умолчению.
     */
    const CITY_BY_DEFAULT = 'Барнаул';

    /**
     * @__construct
     */
    public function __construct()
    {
        $this->isFromAdDetect();

        $this->page = Pages::where('alias', '=', $_SERVER['REQUEST_URI'])->remember(120)->first();
        if (isset($this->page)) {
            $this->title = $this->page->meta_title;
            $this->keywords = $this->page->meta_keywords;
            $this->description = $this->page->meta_descripion;
        }

        $this->cityDetect();

        parent::__construct();
    }

    /**
     * Установит флаг в куки, откуда пришел пользователь.
     */
    public function isFromAdDetect()
    {
        //  Флаг определяющий, что заказ сделал пользователь привлеченный из маркета.
        if (\Request::has('utm_campaign') || \Request::has('ad_id')) {
            \Cookie::queue('is_from_direct', 1, 360);
            \Cookie::queue('direct_campaign', \Input::get('utm_campaign'), 360);        //  Псевдоним кампании
            \Cookie::queue('direct_ad_id', \Input::get('ad_id'), 360);                  //  Идентификатор объявления
        } elseif (\Request::has('frommarket')) {
            \Cookie::queue('is_from_market', 1, 360);
        } elseif (\Request::has('gclid')) {
            \Cookie::queue('is_from_adwords', 1, 360);
        }
    }

    /**
     * @inheritdoc
     */
    public function render($viewName, $params = [])
    {
        $isAvailableOC = \Cache::remember('onlineconsult', 6, function () {
            $result = false;
            try {
                get_headers('http://' . \Config::get('jabber.host') . ':8080/socket.io/socket.io.js');
                $result = true;
            } catch (\Exception $e) {
                //
            }

            return $result;
        });

        return parent::render(
            $viewName, array_merge(
                [
                    'city' => $this->city,
                    'cities' => Cities::getAll(),
                    'page' => $this->page,
                    'isAvailableOC' => $isAvailableOC
                ], $params
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function renderError($viewName, $params = [], $code = 500)
    {
        return parent::renderError(
            $viewName, array_merge(
            [
                'city' => $this->city,
                'cities' => Cities::getAll(),
                'page' => $this->page
            ], $params
        ), $code
        );
    }

    /**
     * Автоопределение города.
     */
    private function cityDetect()
    {
        $cityId = \Cookie::get('city_id');

        //  Если город записан в куках.
        if (isset($cityId)) {
            $this->city = Cities::where('id', $cityId)->remember(120)->first();
        } else {
            //  Если в куках нет, то определяем.
            $geo = new SxGeo(app_path() . '/database/SxGeoCity.dat', SXGEO_BATCH | SXGEO_MEMORY);
            $city = $geo->get(\Request::getClientIp());
            $this->city = Cities::where('name', '=', $city['city']['name_ru'])->remember(120)->first();
        }

        //  Если город по каким то причинам не был найден, установим город по умолчанию.
        if (!isset($this->city)) {
            $this->city = Cities::getCurrentCity();
        }

        //  Если город отключен.
        if (!$this->city->is_visible) {
            \Session::flash('city_off', 1);
            \Cookie::queue('city_id', 0, 60 * 24 * 30);
            \Cookie::queue('city_alias', '', 60 * 24 * 30);
        } else {
            //  Запишем в куки.
            \Cookie::queue('city_id', $this->city->id, 60 * 24 * 30);
            \Cookie::queue('city_alias', $this->city->alias, 60 * 24 * 30);
        }

        return \Redirect::to('/');
    }
}