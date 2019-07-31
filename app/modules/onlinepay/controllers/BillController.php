<?php

namespace modules\onlinepay\controllers;

/**
 * Модуль оплаты наличными.
 */
class BillController extends \modules\onlinepay\components\BaseController
{
	/**
	 * @inheritdoc
	 */
	public static function getPaymentName()
	{
		return 'Выставить счет';
	}

	/**
	 * @inheritdoc
	 */
	public static function getPaymentDesc()
	{
		return 'Выставить счет для оплаты заказа';
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
		return isset(\Sentry::getUser()->is_firm) && \Sentry::getUser()->is_firm;
	}
}