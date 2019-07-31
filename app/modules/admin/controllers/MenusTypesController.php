<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\admin\controllers;

use models\MenusTypes;

/**
 * Контроллер типов меню.
 */
class MenusTypesController extends \modules\admin\components\BaseController
{
    /**
     * Создание нового типа меню.
     *
     * @return \Illuminate\View\View
     */
    public function anyCreate()
    {
        $this->ajaxOnly();

        $model = new MenusTypes;

        if (\Request::isMethod('POST')) {
            return (!$model->load(\Input::all()) || !$model->save())
                ? $this->renderAjax('create', ['model' => $model], false)
                : $this->answerAjax('Меню успешно создано', true);
        }

        return $this->renderAjax(
            'create', [
                'model' => $model
            ]
        );
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
        $this->ajaxOnly();

        $model = $this->loadModel($id);

        if (\Request::isMethod('POST')) {
            return (!$model->load(\Input::all()) || !$model->save())
                ? $this->renderAjax('update', ['model' => $model])
                : $this->answerAjax('Изменения сохранены', true);
        }

        return $this->renderAjax('update', ['model' => $model]);
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
            $this->loadModel($id)->delete();
        } catch (\Exception $e) {
            return $this->errorAjax('Не удалось удалить категорию. ' . $e->getMessage());
        }

        return $this->redirectAjax('/admin/menus');
    }
}
