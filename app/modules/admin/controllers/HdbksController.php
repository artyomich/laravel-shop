<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\admin\controllers;

use components\ActiveRecord;
use models\Categories;
use models\Products;
use Whoops\Example\Exception;

/**
 * Контроллер категорий.
 */
class HdbksController extends \modules\admin\components\BaseController
{
    /**
     * @var array домашняя ссылка для хлебных крошек.
     */
    protected $homeLink
        = ['label' => '<i class="glyphicon glyphicon-book"></i> Справочники', 'link' => '/admin/hdbks/'];

    /**
     * Главная страница со списком заказов.
     *
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        $this->title = 'Справочники';

        return $this->render('index');
    }
}
