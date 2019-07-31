<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\admin\controllers;

use models\Categories;
use models\Employers;
use models\Images;
use models\Products;

/**
 * Контроллер товаров.
 */
class HdbkEmployersController extends \modules\admin\components\BaseController
{
    /**
     * @var array
     */
    protected $homeLink
        = ['label' => '<i class="glyphicon glyphicon-book"></i> Справочники', 'link' => '/admin/hdbks/'];

    /**
     * Редактирование города.
     *
     * @param integer $id
     *
     * @return \Illuminate\View\View
     */
    public function anyUpdate($id)
    {
        $model = $this->loadModel($id);

        $this->title = $model->name;
        $this->breadcrumbs[] = 'Сотрудники';
        $this->breadcrumbs[] = $this->title;
        $viewParams = [
            'model' => $model,
        ];

        if (\Request::isMethod('post')) {
            $model->load(\Input::all());
            if ($model->save()) {
                return $this->redirect('/admin/hdbkemployers/update/' . $model->id);
            }
        }

        return $this->render('form', $viewParams);
    }

    /**
     * Создание нового сотрудника.
     *
     * @return \Illuminate\View\View
     */
    public function anyCreate()
    {
        $this->title = 'Новый сотрудник';
        $this->breadcrumbs[] = $this->title;

        $model = new Employers;
        $viewParams = [
            'model' => $model
        ];

        if (\Request::isMethod('post')) {
            return !$model->load(\Input::all()) || !$model->save()
                ? $this->render('form', $viewParams, false)
                : $this->redirect('/admin/hdbkemployers/update/' . $model->id);
        }

        return $this->render('form', $viewParams);
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

    /**
     * Сортировка списка.
     *
     * @return \Illuminate\View\View
     */
    public function postSort()
    {
        $post = \Input::all();

        if (!isset($post['sort'])) {
            return $this->errorAjax('Ошибка сортировки. Не переданы данные.');
        }

        foreach ($post['sort'] as $key => $id) {
            $model = $this->loadModel($id);
            $model->sorting = $key;
            if (!$model->save()) {
                $this->errorAjax('Не удалось отсортировать меню.');
            }
        }

        return $this->answerAjax();
    }

    /**
     * @inheritdoc
     */
    public static function modelName()
    {
        return Employers::className();
    }
}
