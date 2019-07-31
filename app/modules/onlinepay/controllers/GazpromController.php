<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\onlinepay\controllers;

use helpers\ArrayHelper;
use models\Orders;
use models\Cities;
use models\Transactions;
use SoapBox\Formatter\Formatter;
use TijsVerkoyen\CssToInlineStyles\Exception;

/**
 * Модуль оплаты банка "Газпром".
 */
class GazpromController extends \modules\onlinepay\components\BaseController
{
	/**
	 * @const bool флаг тестового режима.
	 */
	const IS_TEST = false;

    /**
     * @var Cities
     */
    private $_city;

    /**
     * @var string
     */
    private $_merch_id;

    /**
     * @var string
     */
    private $_account_id;

	/**
	 * @inheritdoc
	 */
	public static function getPaymentName()
	{
		return 'Оплата банковской картой Visa или Mastercard';
	}

	/**
	 * @inheritdoc
	 */
	public static function getPaymentDesc()
	{
		return '<div class="row">
<div class="col-xs-1"><span class="thumbnail" style="margin-bottom:0"><img src="/img/onlinepay/1.gif"/></span></div>
<div class="col-xs-1"><span class="thumbnail" style="margin-bottom:0"><img src="/img/onlinepay/2.jpg"/></span></div>
<div class="col-xs-1"><span class="thumbnail" style="margin-bottom:0"><img src="/img/onlinepay/3.gif"/></span></div>
<div class="col-xs-1"><span class="thumbnail" style="margin-bottom:0"><img src="/img/onlinepay/4.jpg"/></span></div>
</div>Платежи проводятся посредством сервиса Газпромбанк (Акционерное общество). Безопасность платежей обеспечивается современными протоколами и технологиями,
разработанными международными платежными системами Visa International и MasterCard Worldwide (3D-Secure: Verified by VISA, MasterCard SecureCode).
Обработка полученных конфиденциальных данных Держателя карты производится в процессинговом центре Банка-эквайера, сертифицированного по стандарту PCI DSS.
Защита передаваемой информации обеспечивается с помощью современных протоколов обеспечения безопасности в Интернет.
';
	}

	/**
	 * @inheritdoc
	 */
	public function getUrlPay()
	{
        $city = Cities::getCurrentCity();
        $params = ArrayHelper::getValue($this->params, $city->alias, $this->params['default']);

		$params = [
			'lang' => 'RU',
            'merch_id' => $params['merch_id'],
			'back_url_s' => 'http://poshk.ru/onlinepay/gazprom/success/?merchant_trx=' . $this->transaction->id,
			'back_url_f' => 'http://poshk.ru/onlinepay/gazprom/fail/?merchant_trx=' . $this->transaction->id,
			'o.merchant_trx' => $this->transaction->id,
            //'o.order_id' => $this->transaction->order_id,
            'o.city_id' => $city->id,
		];

		$url = 'https://www.pps.gazprombank.ru:443/payment/start.wsm';
		if (self::IS_TEST) {
			$url = 'https://test.pps.gazprombank.ru:443/payment/start.wsm';
		}

		return $url . '?' . http_build_query($params);
	}

	/**
	 * CheckPaymentAvailUrl
	 */
	public function anyCheck()
	{
		$this->checkSecurity();

		$result = Formatter::make([
			'payment-avail-response' => [
				'result' => [
					'code' => '1',
					'desc' => 'OK'
				],
				'merchant-trx' => $this->transaction->id,
				//'submerchant' => 'POSHK',
				'purchase' => [
					'longDesc' => 'Оплата заказа N' . $this->transaction->order->id,
					'shortDesc' => ' ',
					'account-amount' => [
                        'id' => $this->_account_id,
						'amount' => (int)$this->transaction->order->cost * 100,
						'currency' => 643,
						'exponent' => 2
					]
				]
			]
		], Formatter::ARR)->toXml();

		//	Тут я понял, что рутовый нод не поменять, по этому делаем так.
		$result = str_replace('<xml>', '', $result);
		$result = str_replace('</xml>', '', $result);
		//$result = substr($result, 39);

		return $result;
	}

	/**
	 * RegisterPaymentUrl
	 */
	public function anyRegister()
	{
		$this->checkSecurity();

		$isPaid = \Request::get('result_code') == 1;
		$amount = (int)\Request::get('amount');

		if (($this->transaction->cost * 100) != $amount) {
			return $this->registerPaymentFail('Сумма оплаты не совпадает с суммой транзакции.');
		}

		return $this->registerPaymentOK($isPaid);
	}

	/**
	 * @throws Exception
	 */
	private function checkSecurity()
	{
        $this->_city = Cities::find(\Request::get('o_city_id'));
        if (!$this->_city) {
            throw new \Exception('Город не найден :: ID ' . \Request::get('o_city_id'));
        }

        $params = ArrayHelper::getValue($this->params, $this->_city->alias, $this->params['default']);
        $this->_merch_id = $params['merch_id'];
        $this->_account_id = $params['account_id'];

		//	Проверка ключей.
        if ($this->_merch_id !== \Request::get('merch_id')) {
            throw new Exception('Неверный merch_id :: ' . \Request::get('merch_id') . ' :: ' . $this->_merch_id);
		}

		//	Проверка транзакции.
		$trxId = \Request::get('merchant_trx', \Request::get('o_merchant_trx'));

		$this->transaction = Transactions::find($trxId);
		if (!$this->transaction || $this->transaction->status != Transactions::STATUS_NEW) {
            throw new \Exception('Транзакция не найдена или уже не актуальна №' . $trxId);
		}
	}

	/**
	 * Вернет успешный ответ для регистрации платежа.
	 * @param boolean $isPaid заказ оплачен
	 * @return string
	 */
	private function registerPaymentOK($isPaid)
	{
		$this->transaction->status = $isPaid ? Transactions::STATUS_SUCCESS : Transactions::STATUS_FAIL;

		return Formatter::make([
			'register-payment-response' => [
				'result' => [
					'code' => 1,
					'desc' => $this->transaction->save() ? 'OK' : 'Ошибка транзакции на стороне сервера'
				]
			]
		], Formatter::ARR)->toXml();
	}

	/**
	 * Вернет успешный ответ для регистрации платежа.
	 * @return string
	 */
	private function registerPaymentFail($comment)
	{
		$this->transaction->comment = $comment;
		$this->transaction->status = Transactions::STATUS_FAIL;
		$this->transaction->save();

		return Formatter::make([
			'register-payment-response' => [
				'result' => [
					'code' => 2,
					'desc' => $comment
				]
			]
		], Formatter::ARR)->toXml();
	}

	/**
	 * @inheritdoc
	 */
	public function getSuccess()
	{
		return $this->render('success');
	}

	/**
	 * @inheritdoc
	 */
	public function getFail()
	{
		return $this->render('fail');
	}

    /**
     * @inheritdoc
     */
	static function paymentIsEnable()
	{
        return !ArrayHelper::getValue(\Sentry::getUser(), 'is_firm', false) && ArrayHelper::getValue(Cities::getCurrentCity(), 'enable_acquiring', false);
	}
}
