<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\admin\controllers;

use components\ActiveRecord;
use models\Banners;
use models\BannersGroups;
use models\Opinions;

/**
 * Контроллер отзывов.
 */
class OpinionsController extends \modules\admin\components\BaseController
{
    /**
     * @var array
     */
    protected $homeLink
        = ['label' => '<i class="glyphicon glyphicon-comment"></i> Отзывы', 'link' => '/admin/opinions/'];

    /**
     * Главная страница со списком отзывов.
     *
     * @return \Illuminate\View\View
     */
    public function anyIndex()
    {
        $this->title = 'Отзывы';

        return \Request::ajax()
            ? $this->renderAjax('index')
            : $this->render('index');
    }

    /**
     * Создание нового отзыва.
     *
     * @return \Illuminate\View\View
     */
    public function anyCreate()
    {
        $this->title = 'Новый отзыв';
        $this->breadcrumbs[] = $this->title;

        $model = new Opinions;
        $viewParams = [
            'model' => $model
        ];

        if (\Request::isMethod('post')) {
            return !$model->load(\Input::all()) || !$model->save()
                ? $this->render('form', $viewParams, false)
                : $this->redirect('/admin/opinions/update/' . $model->id);
        }

        return $this->render('form', $viewParams);
    }

    /**
     * Редактирование существующего отзыва.
     *
     * @return \Illuminate\View\View
     */
    public function anyUpdate($id)
    {
        $model = $this->loadModel($id);

        $this->title = $model->name;
        $this->breadcrumbs[] = $this->title;
        $viewParams = [
            'model' => $model
        ];

        if (\Request::isMethod('post')) {
            $model->load(\Input::all());
            if ($model->save()) {
                return $this->redirect('/admin/opinions/update/' . $model->id);
            }
        }

        return \Request::ajax()
            ? $this->renderAjax('form', $viewParams)
            : $this->render('form', $viewParams);
    }

    /**
     * Удаление существующего банера.
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
            return $this->errorAjax('Не удалось удалить отзыв. ' . $e->getMessage());
        }

        return $this->answerAjax('Отзыв был успешно удален');
    }
}
