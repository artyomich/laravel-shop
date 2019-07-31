<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace widgets\modals;

use models\Cities;
use widgets\BaseModal;

/**
 * @package widgets\modals
 */
class AuthPageModal extends BaseModal
{
    /**
     * @inheritdoc
     */
    public function getButtonName()
    {
        return 'Авторизация';
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        return parent::run();
    }
}