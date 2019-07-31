<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\main\controllers;

/**
 * 500 ошибка.
 */
class FailController extends \modules\main\components\BaseController
{
    /**
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        $this->title = 'Ошибка на сайте';
        return $this->renderError('index', [], 500);
    }
}
