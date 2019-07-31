<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\admin\controllers;

use models\Categories;
use models\Images;
use models\Products;

/**
 * Контроллер товаров.
 */
class ProductsController extends \modules\admin\components\BaseController
{
    /**
     * @var array
     */
    protected $homeLink
        = ['label' => '<i class="glyphicon glyphicon-list-alt"></i> Товары', 'link' => '/admin/products'];

    /**
     * Главная страница со списком товаров.
     *
     * @return \Illuminate\View\View
     */
    public function anyIndex()
    {
        $this->title = 'Каталог';

        return \Request::ajax()
            ? $this->renderAjax('index', ['categories' => Categories::getAll()])
            : $this->render('index', ['categories' => Categories::getAll()]);
    }

    /**
     * Создание нового товара.
     *
     * @return \Illuminate\View\View
     */
    public function anyCreate()
    {
        $this->title = 'Новый товар';
        $this->breadcrumbs[] = $this->title;

        $model = new Products;
        $viewParams = [
            'model'      => $model,
            'categories' => Categories::getAll()
        ];

        if (\Request::isMethod('post')) {
            return !$model->load(\Input::all()) || !$model->save()
                ? $this->render('form', $viewParams, false)
                : $this->redirect('/admin/products/update/' . $model->id);
        }

        return $this->render('form', $viewParams);
    }

    /**
     * Редактирование существующего контроллера.
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
            'categories' => Categories::getAll()
        ];

        if (\Request::isMethod('post')) {
            $model->load(\Input::all());
            if ($model->save()) {
                return $this->redirect('/admin/products/update/' . $model->id);
            }
        }

        return \Request::ajax()
            ? $this->renderAjax('form', $viewParams)
            : $this->render('form', $viewParams);
    }

    /**
     * Удаление существующего товара.
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
            return $this->errorAjax('Не удалось удалить товар. ' . $e->getMessage());
        }

        return $this->answerAjax('Товар был успешно удален');
    }
}
