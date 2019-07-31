<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

use TijsVerkoyen\CssToInlineStyles\Exception;

/**
 * ActiveRecord таблицы `users`
 *
 * @property integer order_id
 * @property string date_create
 * @property string date_update
 * @property boolean is_done
 * @property string comment
 * @property string status
 *
 * @package models
 */
class Transactions extends \components\ActiveRecord
{
	/**
	 * @var string название таблицы.
	 */
	protected $table = 'transactions';

	/**
	 * @const string
	 */
	const STATUS_NEW = 'new';

	/**
	 * @const string
	 */
	const STATUS_SUCCESS = 'success';

	/**
	 * @const string
	 */
	const STATUS_FAIL = 'fail';

	/**
	 * @var bool
	 */
	public $timestamps = true;

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['order_id', 'exists:orders,id'],
			//[['date_create', 'date_update'], 'required|date'],
			['status', 'in:new,success,fail'],
			['is_done', 'boolean'],
		];
	}

	/**
	 * Создаст новую транзакцию.
	 *
	 * @param integer $orderId
	 * @param integer $cost
	 * @param string $comment
	 * @return array|self
	 * @throws Exception
	 */
	public static function init($orderId, $cost, $comment)
	{
		//	Проверка заказа на существование.
		/** @var Orders $order */
		$order = Orders::find(['id' => $orderId])->first();
		if (!$order) {
			throw new Exception('Такого заказа не существует');
		}

		//	Поиск транзакции этого заказа для предотвращения повторной оплаты.
		if (Transactions::where(['order_id' => $orderId, 'status' => self::STATUS_SUCCESS])->count()) {
			throw new \Exception('Этот заказ уже был оплачен');
		}

		$model = new self;
		$model->status = self::STATUS_NEW;
		$model->order_id = $orderId;
		$model->comment = $comment;
		$model->cost = $cost ? $cost : $order->cost;

		if ($model->cost < 1) {
			throw new Exception('Неверная сумма заказа');
		}

		$model->save();

		return $model;
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\hasOne
	 */
	public function order()
	{
		return $this->hasOne(Orders::className(), 'id', 'order_id');
	}
}