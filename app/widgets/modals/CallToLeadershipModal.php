<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace widgets\modals;

use widgets\BaseModal;

/**
 * @package widgets\modals
 */
class CallToLeadershipModal extends BaseModal
{
    /**
     * @return string
     */
    public function getButtonName()
    {
        return 'Обратиться к руководству';
    }
}