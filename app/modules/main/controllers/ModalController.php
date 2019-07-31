<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\main\controllers;

use components\Widget;
use widgets\BaseModal;

/**
 * Class ModalController
 * @package modules\main\controllers
 */
class ModalController extends \modules\main\components\BaseController
{
    /**
     * @return string
     */
    public function postIndex()
    {
        $modals = \Config::get('modals');
        if (!\Input::get('name') || !in_array(\Input::get('name'), $modals)) {
            return '{}';
        }

        /** @var BaseModal $className */
        $className = '\\widgets\\modals\\' . \Input::get('name');
        return $this->renderAjax('index', [
            'modal' => $className::widget()
        ]);
    }
}