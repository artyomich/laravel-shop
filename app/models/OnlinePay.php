<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

use modules\onlinepay\components\BaseController;
use modules\onlinepay\controllers\GazpromController;

class OnlinePay
{
    /**
     * Вернет все методы оплаты.
     * @return array
     */
    public static function getMethods()
    {
        return array_keys(\Config::get('onlinepay.methods'));
    }

    /**
     * Вернет параметры метода оплаты.
     * @param string $className
     * @return array
     */
    public static function getMethodParams($className)
    {
        $methods = \Config::get('onlinepay.methods');
        return isset($methods[$className]) ? $methods[$className] : [];
    }

	/**
	 * Вернет url для оплаты по алиасу.
	 * @param $alias
	 * @param $orderId
	 * @return string
	 */
	public static function getPayUrlByAlias($alias, $orderId)
	{
		$payments = self::getMethods();
		/** @var BaseController $pay */
		foreach ($payments as $pay) {
			if ($pay::getPaymentAlias() == $alias) {
				return $pay::getMethodUrl($orderId);
			}
		}
		return '/';
	}
}