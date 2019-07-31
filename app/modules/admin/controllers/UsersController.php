<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\admin\controllers;

use components\ActiveRecord;
use models\Categories;
use models\Cities;
use models\Groups;
use models\Products;
use models\Users;
use models\UsersGroups;
use Whoops\Example\Exception;

/**
 * Контроллер пользователей.
 */
class UsersController extends \modules\admin\components\BaseController
{
    /**
     * @var array домашняя ссылка для хлебных крошек.
     */
    protected $homeLink
        = ['label' => '<i class="fa fa-users"></i> Пользователи', 'link' => '/admin/users/'];

    /**
     * Главная страница со списком пользователей.
     *
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        $this->title = 'Пользователи';
        $params = [
            'groups' => Groups::orderBy('created_at')->get()
        ];

        return $this->render('index', $params);
    }

    /**
     * Создание нового пользователя.
     *
     * @return \Illuminate\View\View|string
     */
    public function anyCreate()
    {
        $this->title = 'Новый пользователь';
        $this->breadcrumbs[] = $this->title;

        $model = new Users;
        $viewParams = [
            'model'  => $model,
            'groups' => Groups::orderBy('created_at')->get()
        ];

        if (\Request::isMethod('POST')) {
            $model->groups = array_keys(\Input::get('UsersGroups', []));
            return !$model->load(\Input::all()) || !$model->saveUser()
                ? $this->renderAjax('form', $viewParams, false)
                : $this->redirectAjax('/admin/users/');
        }

        return \Request::ajax()
            ? $this->renderAjax('form', $viewParams)
            : $this->render('form', $viewParams);
    }

    /**
     * Редактирование существующего пользователя.
     *
     * @return \Illuminate\View\View
     */
    public function anyUpdate($id)
    {
        $model = $this->loadModel($id);

        $this->title = $model->name;
        $this->breadcrumbs[] = $this->title;
        $viewParams = [
            'model'  => $model,
            'cities' => Cities::all(),
            'groups' => Groups::orderBy('created_at')->get()
        ];

        if (\Request::isMethod('POST')) {
            $model->groups = array_keys(\Input::get('UsersGroups', []));
            return !$model->load(\Input::all()) || !$model->saveUser()
                ? $this->renderAjax('form', $viewParams, false)
                : $this->redirectAjax('/admin/users/');
        }

        return $this->renderAjax('form', $viewParams);
    }

    /**
     * Обновление группы.
     *
     * @param integer $id
     */
    public function anyGroupupdate($id)
    {
        $model = $this->loadGroup($id);

        $this->title = $model->name;
        $this->breadcrumbs[] = $this->title;
        $viewParams = [
            'model' => $model,
        ];

        if (\Request::isMethod('POST')) {
            return (!$viewParams['model']->load(\Input::all()) || !$viewParams['model']->save())
                ? $this->renderAjax('update', $viewParams)
                : $this->answerAjax('Изменения сохранены', true);
        }

        return \Request::ajax()
            ? $this->renderAjax('form-group', $viewParams)
            : $this->render('form-group', $viewParams);
    }

    /**
     * Удаление существующего пользователя.
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
            return $this->errorAjax('Не удалось удалить пользователя. ' . $e->getMessage());
        }

        return $this->answerAjax('Пользователь был успешно удален');
    }

    /**
     * Загрузит модель группы по идентификатору.
     *
     * @param $id
     *
     * @return Groups
     */
    public function loadGroup($id)
    {
        /** @var ActiveRecord $model */
        $model = Groups::find($id);
        if (!$model) {
            \App::abort(404, 'Группа не найдена');
        }

        return $model;
    }

    /**
     * @param $id
     * @return Users
     */
    public function loadModel($id)
    {
        /** @var ActiveRecord $model */
        $modelName = $this->modelName();
        $model = $modelName::with('groups')->find($id);
        if (!$model) {
            \App::abort(404, 'Model not found');
        }

        return $model;
    }
}
