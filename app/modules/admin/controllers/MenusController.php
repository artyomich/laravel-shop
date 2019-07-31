<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\admin\controllers;

use models\Menus;
use models\MenusTypes;

/**
 * Контроллер товаров.
 */
class MenusController extends \modules\admin\components\BaseController
{
    /**
     * @var array
     */
    protected $homeLink
        = ['label' => '<i class="glyphicon glyphicon-list-alt"></i> Навигация', 'link' => '/admin/menus'];

    /**
     * Главная страница со списком пунктов меню.
     *
     * @return \Illuminate\View\View
     */
    public function anyIndex()
    {
        $this->title = 'Навигация';

        return \Request::ajax()
            ? $this->renderAjax('index', ['types' => MenusTypes::get()])
            : $this->render('index', ['types' => MenusTypes::get()]);
    }

    /**
     * Создание нового пункта меню.
     *
     * @return \Illuminate\View\View
     */
    public function anyCreate()
    {
        $this->title = 'Новый пункт меню';
        $this->breadcrumbs[] = $this->title;

        $model = new Menus;
        $viewParams = [
            'model' => $model,
            'types' => MenusTypes::get(),
            'menus' => Menus::whereNull('parent_id')->get()
        ];

        if (\Request::isMethod('post')) {
            return !$model->load(\Input::all()) || !$model->save()
                ? $this->renderAjax('form', $viewParams, false)
                : $this->redirectAjax('/admin/menus/update/' . $model->id);
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
            'model' => $model,
            'types' => MenusTypes::get(),
            'menus' => Menus::where('id', '!=', $model->id)->whereNull('parent_id')->get()
        ];

        if (\Request::isMethod('post')) {
            $model->load(\Input::all());
            if ($model->save()) {
                return \Request::ajax()
                    ? $this->redirectAjax('/admin/menus/update/' . $model->id)
                    : $this->redirect('/admin/menus/update/' . $model->id);
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
            return $this->errorAjax('Не удалось удалить пункт меню. ' . $e->getMessage());
        }

        return $this->answerAjax('Пункт меню был успешно удален');
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
}
