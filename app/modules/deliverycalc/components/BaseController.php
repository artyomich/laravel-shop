<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\deliverycalc\components;

use models\Cities;
use models\Transactions;

abstract class BaseController extends \modules\main\components\BaseController
{
    /**
     * Вернет название ТК.
     * @return string
     * @throws \Exception
     */
    static function getDeliveryName()
    {
        throw new \Exception('Не задано имя модуля доставки.');
    }

    /**
     * Функция рассчета стоимости доставки.
     * @return string
     */
    public function getCalc()
    {
        //  TODO:
        return '';
    }
}