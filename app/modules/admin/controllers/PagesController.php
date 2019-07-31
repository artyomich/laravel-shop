<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\admin\controllers;

use models\Menus;
use models\MenusTypes;
use models\Pages;

/**
 * Контроллер товаров.
 */
class PagesController extends \modules\admin\components\BaseController
{
    /**
     * @var array
     */
    protected $homeLink
        = ['label' => '<i class="glyphicon glyphicon-file"></i> Страницы', 'link' => '/admin/pages'];

    /**
     * Главная страница со списком пунктов меню.
     *
     * @return \Illuminate\View\View
     */
    public function anyIndex()
    {
        $this->title = 'Страницы';
        $params = [
            'categories' => \models\PagesCategories::all()
        ];

        return \Request::ajax()
            ? $this->renderAjax('index', $params)
            : $this->render('index', $params);
    }

    /**
     * Создание нового пункта меню.
     *
     * @return \Illuminate\View\View
     */
    public function anyCreate()
    {
        $this->title = 'Новая страница';
        $this->breadcrumbs[] = $this->title;

        $model = new Pages;
        $viewParams = [
            'model'      => $model,
            'categories' => \models\PagesCategories::all()
        ];

        if (\Request::isMethod('post')) {
            return !$model->load(\Input::all()) || !$model->save()
                ? $this->render('form', $viewParams, false)
                : $this->redirect('/admin/pages/update/' . $model->id);
        }

        return $this->render('form', $viewParams);
    }

    /**
     * Редактирование существующего пункта меню.
     *
     * @return \Illuminate\View\View
     */
    public function anyUpdate($id)
    {
        $model = $this->loadModel($id);

        $this->title = $model->name;
        $this->breadcrumbs[] = $this->title;
        $viewParams = [
            'model'      => $model,
            'categories' => \models\PagesCategories::all()
        ];

        if (\Request::isMethod('post')) {
            $model->load(\Input::all());
            if ($model->save()) {
                return $this->redirect('/admin/pages/update/' . $model->id);
            }
        }

        return \Request::ajax()
            ? $this->renderAjax('form', $viewParams)
            : $this->render('form', $viewParams);
    }

    /**
     * Удаление существующего пункта меню.
     *
     * @param integer $id
     *
     * @return string
     */
    public function postDelete($id)
    {
        try {
            $this->loadModel($id)->delete();
        } catch (\Exception $e) {
            return $this->errorAjax('Не удалось удалить страницу. ' . $e->getMessage());
        }

        return $this->answerAjax('Страница была успешно удалена.');
    }
}
