<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\main\controllers;

/**
 * Контроллер товаров.
 */
class NotFoundController extends \modules\main\components\BaseController
{
    /**
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        $this->title = 'Товар не найден';
        return $this->renderError('index', [], 404);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function getServerError()
    {
        $this->title = 'Ошибка на сайте';
        return $this->renderError('fail', [], 500);
    }
}
