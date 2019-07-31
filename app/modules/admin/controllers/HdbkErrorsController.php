<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\admin\controllers;

use models\LogErrors;

/**
 * Контроллер 404 страниц.
 */
class HdbkErrorsController extends \modules\admin\components\BaseController
{
    /**
     * @var array
     */
    protected $homeLink
        = ['label' => '<i class="glyphicon glyphicon-book"></i> Справочники', 'link' => '/admin/hdbks/'];

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
            return $this->errorAjax('Не удалось удалить запись. ' . $e->getMessage());
        }

        return $this->answerAjax('Запись была успешно удалена');
    }

    /**
     * @inheritdoc
     */
    public static function modelName()
    {
        return LogErrors::className();
    }
}
