<?php
/**
 * @author Artyom Arifulin <arifulin@gmail.com>
 */

namespace widgets\modals;

use models\Cities;
use widgets\BaseModal;

/**
 * @package widgets\modals
 */
class NotifyProductsModal extends BaseModal
{
    /**
     * @inheritdoc
     */
    public function getButtonName()
    {
        return '<button type="button" class="btn btn-primary btn-cart-oformit font12">
                                    Уведомить о поступлении
                                </button>';
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