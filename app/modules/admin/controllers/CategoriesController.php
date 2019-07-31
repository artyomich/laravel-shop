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
class CategoriesController extends \modules\admin\components\BaseController
{
    /**
     * Главная страница со списком категорий.
     *
     * @return \Illuminate\View\View
     */
    public function anyIndex()
    {
        $this->title = 'Категории';
        $this->breadcrumbs[] = 'Категории';

        return $this->render(
            'index', [
                'categories' => Categories::getAll()
            ]
        );
    }

    /**
     * Создание новой категории.
     *
     * @return \Illuminate\View\View
     */
    public function anyCreate()
    {
        if (!\Request::ajax()) {
            \App::abort(500, 'This is not ajax request');
        }

        $getModel = \Input::get('model');
        if (isset($getModel)) {
            $modelClassName = $getModel;
            $model = new $modelClassName();
        } else {
            $model = new Categories();
        }

        $params = [
            'model'     => $model,
            'modelName' => isset($modelClassName) ? $modelClassName : null
        ];

        if (\Request::isMethod('POST')) {
            return (!$model->load(\Input::all()) || !$model->save())
                ? $this->renderAjax('create', $params, false)
                : $this->answerAjax('Категория успешно создана', true);
        }

        return $this->renderAjax('create', $params);
    }

    /**
     * Редактирование существующей категории.
     *
     * @param $id
     *
     * @return string
     */
    public function anyUpdate($id)
    {
        if (!\Request::ajax()) {
            \App::abort(500, 'This is not ajax request');
        }

        $params = [
            'model'     => $this->getModel($id, \Input::get('model')),
            'modelName' => \Input::get('model')
        ];

        if (\Request::isMethod('POST')) {
            return (!$params['model']->load(\Input::all()) || !$params['model']->save())
                ? $this->renderAjax('update', $params)
                : $this->answerAjax('Изменения сохранены', true);
        }

        return $this->renderAjax('update', $params);
    }

    /**
     * Удаление существующей категории.
     *
     * @param integer $id
     *
     * @return string
     */
    public function postDelete($id)
    {
        try {
            $this->getModel($id, \Input::get('model'))->delete();
        } catch (\Exception $e) {
            return $this->errorAjax('Не удалось удалить категорию. ' . $e->getMessage());
        }

        $redirect = \Input::get('redirect');

        return $this->redirectAjax(isset($redirect) ? $redirect : '/admin/products');
    }

    /**
     * Сортировка списка категорий.
     *
     * @return \Illuminate\View\View
     */
    public function postSort()
    {
        if (!\Request::ajax()) {
            \App::abort(500, 'This is not ajax request');
        }

        if (\Request::isMethod('POST')) {
            $post = \Input::all();

            foreach ($post['sort'] as $key => $id) {
                $model = $this->getModel($id, \Input::get('model'));
                $model->sorting = $key;
                if (!$model->save()) {
                    $this->errorAjax('Не удалось отсортировать категории');
                }
            }

            return $this->answerAjax();
        }
    }

    /**
     * @param integer $id        идентификатор модели
     * @param string  $modelName название модели
     *
     * @return ActiveRecord
     */
    public function getModel($id, $modelName = null)
    {
        if (isset($modelName)) {
            $modelClassName = $modelName;
            return $modelClassName::find($id);
        } else {
            return $this->loadModel($id);
        }
    }
}
