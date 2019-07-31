<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\admin\controllers;

use models\Categories;
use models\Employers;
use models\Filter;
use models\Images;
use models\Products;

/**
 * Контроллер CRUD операций для фильтра шин.
 */
class HdbkFilterController extends \modules\admin\components\BaseController
{
    /**
     * @var array
     */
    protected $homeLink
        = ['label' => '<i class="glyphicon glyphicon-book"></i> Справочники', 'link' => '/admin/hdbks/'];

    /**
     * Редактирование записи.
     *
     * @param integer $id
     *
     * @return \Illuminate\View\View
     */
    public function anyUpdate($id)
    {
        $model = $this->loadModel($id);

        $this->title = $model->alias;
        $this->breadcrumbs[] = 'Фильтр';
        $this->breadcrumbs[] = $this->title;
        $viewParams = [
            'model' => $model,
        ];

        if (\Request::isMethod('post')) {
            $model->load(\Input::all());

            //  Убираем из псевдонима вконце слеш т.к. он убирается при редиректе.
            if (substr($model->alias, -1) == '/') {
                $model->alias = substr($model->alias, 0, -1);
            }

            if ($model->save()) {
                return $this->redirect('/admin/hdbkfilter/update/' . $model->id);
            }
        }

        return $this->render('form', $viewParams);
    }

    /**
     * Создание новой записи.
     *
     * @return \Illuminate\View\View
     */
    public function anyCreate()
    {
        $this->title = 'Новая запись';
        $this->breadcrumbs[] = $this->title;

        $model = new Filter;
        $viewParams = [
            'model' => $model
        ];

        if (\Request::isMethod('post')) {
            if (!$model->load(\Input::all())) {
                return $this->render('form', $viewParams, false);
            }

            //  Убираем из псевдонима вконце слеш т.к. он убирается при редиректе.
            if (substr($model->alias, -1) == '/') {
                $model->alias = substr($model->alias, 0, -1);
            }

            return !$model->save()
                ? $this->render('form', $viewParams, false)
                : $this->redirect('/admin/hdbkfilter/update/' . $model->id);
        }

        return $this->render('form', $viewParams);
    }

    /**
     * Удаление существующей записи.
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
            return $this->errorAjax('Не удалось удалить запись. ' . $e->getMessage());
        }

        return $this->answerAjax('Запись была учпешно удалена');
    }

    /**
     * @inheritdoc
     */
    public static function modelName()
    {
        return Filter::className();
    }
}
