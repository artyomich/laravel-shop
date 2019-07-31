<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\admin\controllers;

use models\Categories;
use models\Employers;
use models\Images;
use models\Products;
use models\Redirects;

/**
 * Контроллер товаров.
 */
class HdbkRedirectsController extends \modules\admin\components\BaseController
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

        $this->title = 'Переадресация #' . $model->id;
        $this->breadcrumbs[] = 'Переадресации';
        $this->breadcrumbs[] = $this->title;
        $viewParams = [
            'model' => $model,
        ];

        if (\Request::isMethod('post')) {
            $model->load(\Input::all());
            if ($model->save()) {
                \Session::flash('success', 'Запись была успешно обновлена');
                return $this->redirect('/admin/hdbkredirects/update/' . $model->id);
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
        $this->title = 'Новая переадресация';
        $this->breadcrumbs[] = $this->title;

        $model = new Redirects;
        $viewParams = [
            'model' => $model
        ];

        if (\Request::isMethod('post')) {
            return !$model->load(\Input::all()) || !$model->save()
                ? $this->render('form', $viewParams, false)
                : $this->redirect('/admin/hdbkredirects/update/' . $model->id);
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
            return $this->errorAjax('Не удалось удалить переадресацию. ' . $e->getMessage());
        }

        return $this->answerAjax('Переадресация была успешно удалена');
    }

    /**
     * @inheritdoc
     */
    public static function modelName()
    {
        return Redirects::className();
    }
}
