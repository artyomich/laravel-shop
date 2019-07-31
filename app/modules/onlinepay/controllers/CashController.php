<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\onlinepay\controllers;
use models\Cities;

/**
 * Модуль оплаты наличными.
 */
class CashController extends \modules\onlinepay\components\BaseController
{
	/**
	 * @inheritdoc
	 */
	public static function getPaymentName()
	{
		return 'Оплата наличными';
	}

	/**
	 * @inheritdoc
	 */
	public static function getPaymentDesc()
	{
		return 'Оплата заказа наличными при получении.';
	}

	/**
	 * @inheritdoc
	 */
	public function getUrlPay()
	{
		return '/order/thanks/' . $this->transaction->order_id . '/';
	}

	/**
	 * @inheritdoc
	 */
	static function paymentIsEnable()
	{
		return true;
	}
}