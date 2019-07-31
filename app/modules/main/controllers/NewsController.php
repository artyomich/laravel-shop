<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\main\controllers;

use components\LogErrorsComponent;
use models\Pages;

/**
 * Контроллер товаров.
 */
class NewsController extends \modules\main\components\BaseController
{
    /**
     * Страница со списком новостей.
     *
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        return $this->render('index');
    }

    /**
     * Для отображения материалов по алиасам :)
     *
     * @param array $parameters
     *
     * @return bool|\Illuminate\View\View|mixed
     */
    public function missingMethod($parameters = [])
    {
        //  Если есть второй параметр, значим ищем статью.
        if (isset($parameters[1])) {
            $alias = '/' . implode('/', array_merge(['news'], $parameters)) . '/';
            $page = Pages::where('alias', '=', $alias)->remember(120)->first();

            if (!isset($page)) {
                LogErrorsComponent::log(404);
                return $this->render('404');
            }

            return $this->render('page', ['page' => $page]);
        }

        //  Cмотрим списки статей.
        return $this->render(
            'list', [
                'pages' => Pages::findByCategoryAlias($parameters[0])
            ]
        );
    }
}
