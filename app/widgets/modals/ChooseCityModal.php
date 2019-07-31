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
class ChooseCityModal extends BaseModal
{
    /**
     * @inheritdoc
     */
    public function getButtonName()
    {
        return '<span>' . $this->city->name . '</span></i>';
    }

    /**
     * @inheritdoc
     */
    public function __construct($params = [])
    {
        $this->__set('city', Cities::getCurrentCity());
        $this->__set('cities', Cities::getAll());
        parent::__construct($params);
    }
}