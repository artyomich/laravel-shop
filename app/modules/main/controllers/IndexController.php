<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\main\controllers;

use models\Products;
use models\Support;

/**
 * Контроллер главной страницы.
 */
class IndexController extends \modules\main\components\BaseController
{
    /**
     * Главная страница со списком товаров.
     *
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        return $this->render(
            'index', [
                'products' => Products::getAllOnIndex()
            ]
        );
    }

    /**
     * Сообщение на почту пошка (обратная связь).
     */
    public function postEmail()
    {
        $model = new Support;
        if (!$model->load(\Input::all())) {
            return $this->renderAjax(':layouts/support_modal', ['supportModel' => $model]);
        }

        $model->sendMessage();
        \Session::flash('success', 'Ваше письмо было отправлено!');

        return $this->answerAjax('OK', true);
    }

    /**
     * Отображение статической страницы или 404ой.
     *
     * @param array $parameters
     *
     * @return bool|\Illuminate\View\View|mixed
     */
    public function missingMethod($parameters = [])
    {
        return $this->render(!isset($this->page) ? '404' : 'page');
    }
}
