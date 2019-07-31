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
class ForwardAdModal extends BaseModal
{
    /**
     * @inheritdoc
     */
    public function getButtonName()
    {
        return '<span>Видео-обзор Forward Safari</span>';
    }
}