<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\main\controllers;

use models\Opinions;

/**
 * Контроллер отзывов.
 */
class OpinionsController extends \modules\main\components\BaseController
{
    /**
     * Список отзывов.
     *
     * @return \Illuminate\View\View
     */
    public function anyIndex()
    {
        $this->title = 'Наши отзывы';
        return $this->render('index');
    }
}