<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\main\controllers;

use models\Categories;
use models\Cities;
use models\Employers;
use models\Products;
use models\ProductsBalances;
use models\ProductsSearch;

/**
 * Контроллер товаров.
 */
class TeamController extends \modules\main\components\BaseController
{
    /**
     * Выбор показа каталога товаров либо отдельного товара.
     *
     * @param array $parameters
     *
     * @return bool|\Illuminate\View\View|mixed
     */
    public function missingMethod($parameters = [])
    {
        $city = Cities::where(['alias' => $parameters[0]])->remember(120)->first();
        if (!isset($city)) {
            return $this->redirect('/');
        }

        $this->title = 'Наша команда, ' . $city->name;

        return $this->render(
            'page', [
                'employers' => Employers::with('image')->where(['city_id' => $city->id])->orderBy('sorting')->get()
            ]
        );
    }
}