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
class ImprovePageModal extends BaseModal
{
    /**
     * @inheritdoc
     */
    public function getButtonName()
    {
        return 'Что можно улучшить на странице?';
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->__set('city', Cities::getCurrentCity());
        return parent::run();
    }
}