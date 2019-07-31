<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\admin\controllers;

use components\ActiveRecord;
use models\Banners;
use models\BannersGroups;

/**
 * Контроллер банеров.
 */
class BannersController extends \modules\admin\components\BaseController
{
    /**
     * @var array
     */
    protected $homeLink
        = ['label' => '<i class="fa fa-th-list"></i> Банеры', 'link' => '/admin/banners/'];

    /**
     * Главная страница со списком банеров.
     *
     * @return \Illuminate\View\View
     */
    public function anyIndex()
    {
        $this->title = 'Банеры';
        $params = [
            'banners' => Banners::orderBy('sorting')->get()
        ];

        return \Request::ajax()
            ? $this->renderAjax('index', $params)
            : $this->render('index', $params);
    }

    /**
     * Создание нового банера.
     *
     * @return \Illuminate\View\View
     */
    public function anyCreate()
    {
        $this->title = 'Новый банер';
        $this->breadcrumbs[] = $this->title;

        $model = new Banners;
        $viewParams = [
            'model' => $model
        ];

        if (\Request::isMethod('post')) {
            return !$model->load(\Input::all()) || !$model->save()
                ? $this->render('form', $viewParams, false)
                : $this->redirect('/admin/banners/update/' . $model->id);
        }

        return $this->render('form', $viewParams);
    }

    /**
     * Редактирование существующего банера.
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
                return $this->redirect('/admin/banners/update/' . $model->id);
            }
        }

        return \Request::ajax()
            ? $this->renderAjax('form', $viewParams)
            : $this->render('form', $viewParams);
    }

    /**
     * Редактирование существующей группы.
     *
     * @param $id
     *
     * @return string
     */
    public function anyGroupupdate($id)
    {
        if (!\Request::ajax()) {
            \App::abort(500, 'This is not ajax request');
        }

        $params = [
            'model' => $this->loadGroup($id)
        ];

        if (\Request::isMethod('POST')) {
            return (!$params['model']->load(\Input::all()) || !$params['model']->save())
                ? $this->renderAjax('update', $params)
                : $this->answerAjax('Изменения сохранены', true);
        }

        return $this->renderAjax('groups-form', $params);
    }

    /**
     * Создание новой группы.
     *
     * @return \Illuminate\View\View
     */
    public function anyGroupcreate()
    {
        if (!\Request::ajax()) {
            \App::abort(500, 'This is not ajax request');
        }

        $model = new BannersGroups;
        $params = [
            'model' => $model
        ];

        if (\Request::isMethod('POST')) {
            return (!$model->load(\Input::all()) || !$model->save())
                ? $this->renderAjax('groups-form', $params, false)
                : $this->answerAjax('Категория успешно создана', true);
        }

        return $this->renderAjax('groups-form', $params);
    }

    /**
     * Удаление существующей категории.
     *
     * @param integer $id
     *
     * @return string
     */
    public function postGroupdelete($id)
    {
        try {
            $this->loadGroup($id)->delete();
        } catch (\Exception $e) {
            return $this->errorAjax('Не удалось удалить группу. ' . $e->getMessage());
        }

        $redirect = \Input::get('redirect');

        return $this->redirectAjax(isset($redirect) ? $redirect : '/admin/banners/');
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
            return $this->errorAjax('Не удалось удалить банер. ' . $e->getMessage());
        }

        return $this->answerAjax('Банер был успешно удален');
    }

    /**
     * Сортировка списка банеров.
     *
     * @return \Illuminate\View\View
     */
    public function postSort()
    {
        if (!\Request::ajax() || !\Request::isMethod('POST')) {
            \App::abort(500, 'This is not ajax request');
        }

        $data = \Input::get('sort');

        foreach ($data as $key => $id) {
            /** @var Banners $model */
            $model = Banners::find($id);
            $model->sorting = $key;
            if (!$model->save()) {
                return $this->errorAjax('Не удалось отсортировать');
            }
        }

        return $this->answerAjax();
    }

    /**
     * Загрузит модель группы банеров.
     *
     * @param $id
     *
     * @return ActiveRecord
     */
    public function loadGroup($id)
    {
        /** @var ActiveRecord $model */
        $model = BannersGroups::find($id);
        if (!$model) {
            \App::abort(404, 'Model not found');
        }

        return $model;
    }
}
