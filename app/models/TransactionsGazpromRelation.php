<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

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
class TransactionsGazpromRelation extends \components\ActiveRecord
{
	/**
	 * @var string название таблицы.
	 */
	protected $table = 'transactions_gazprom_relation';

	/**
	 * @var bool
	 */
	public $timestamps = false;

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['transaction_id, pps_id', 'required'],
		];
	}
}