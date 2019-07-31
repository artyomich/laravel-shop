<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\onlinepay\components;

use models\Cities;
use models\OnlinePay;
use models\Transactions;

abstract class BaseController extends \modules\main\components\BaseController
{
	/**
	 * @var Transactions
	 */
	protected $transaction;

    /**
     * @var array дополнительные параметры.
     */
    protected $params = [];

	/**
	 * Вернет URL на который нужно переадресовать пользователя для оплаты.
	 * @return string
	 */
	abstract function getUrlPay();

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct();

        $this->params = OnlinePay::getMethodParams('\\' . $this->className());
    }

    /**
	 * Вернет название метода оплаты.
	 * @return string
	 * @throws \Exception
	 */
	static function getPaymentName()
	{
		throw new \Exception('Не задано имя модуля.');
	}

	/**
	 * Вернет описание метода оплаты.
	 * @return string
	 * @throws \Exception
	 */
	static function getPaymentDesc()
	{
		throw new \Exception('Не задано описание модуля.');
	}

	/**
	 * Вернет алиас метода оплаты.
	 * @return string
	 * @throws \Exception
	 */
	static function getPaymentAlias()
	{
		$_path = explode('\\', strtolower(substr(get_called_class(), 0, -10)));
		return end($_path);
	}

	/**
	 * Вернет состояние эквайринга для текущего города.
	 */
	static function paymentIsEnable()
	{
		return false;
	}

	/**
	 * Вернет ссылку на метод оплаты, который уже сам все разрулит.
	 * @param $orderId
	 * @return string
	 * @throws \Exception
	 */
	static function getMethodUrl($orderId)
	{
		return '/onlinepay/' . self::getPaymentAlias() . '/pay/' . $orderId . '/';
	}

	/**
	 * Функция для инициализации платежа.
	 * @return string
	 */
	public function getPay($orderId)
	{
		/** @var self $class */
		$class = get_called_class();

		//	Проверяем состояние эквайринга.
		if (!$class::paymentIsEnable()) {
			return \Redirect::to('/');
		}

		//	Инициализируем новую транзакцию.
		/** @var Transactions $transaction */
		$this->transaction = Transactions::init($orderId, null, 'Оплата заказа №' . $orderId);
		if (!$this->transaction || $this->transaction->hasErrors()) {
			throw new \Exception('Не удалось создать транзакцию');
		}

		return \Redirect::to($this->getUrlPay());
	}

	/**
	 * Страница успешного платежа.
	 * @return string
	 */
	public function getSuccess()
	{
		return 'OK';
	}

	/**
	 * Страница неудачного платежа.
	 * @return string
	 */
	public function getFail()
	{
		return 'FAIL';
	}
}