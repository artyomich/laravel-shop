<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\admin\controllers;

/**
 * Контроллер отзывов.
 */
class ProductsOpinionsController extends \modules\admin\components\BaseController
{
    /**
     * @var array
     */
    protected $homeLink
        = ['label' => '<i class="glyphicon glyphicon-comment"></i> Отзывы', 'link' => '/admin/opinions/'];

    /**
     * Подтверждение существующего отзыва.
     *
     * @param integer $id
     *
     * @return string
     */
    public function postConfirm($id)
    {
        try {
            $model = $this->loadModel($id);
            $model->is_checked = true;
            $model->save();
        } catch (\Exception $e) {
            return $this->errorAjax('Не удалось подтвердить отзыв. ' . $e->getMessage());
        }

        return $this->answerAjax('Отзыв был успешно подвержден');
    }

    /**
     * Удаление существующего отзыва.
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
