<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

/**
 * ActiveRecord таблицы `users`
 *
 * @package models
 */
class OnlineConsult extends \components\ActiveRecord
{
	/**
	 * @var string название таблицы.
	 */
	protected $table = 'onlineconsult';

	/**
	 * @var array
	 */
	protected $primaryKey = 'city_id';

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
			[['city_id', 'city_key'], 'required'],
			['is_enable', 'boolean']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function beforeSave()
	{
		$this->is_enable = $this->is_enable ? 't' : 'f';

		return parent::beforeSave();
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'city_id' => 'ID',
			'city_key' => 'Ключ',
			'is_enable' => 'Включен'
		];
	}

	/**
	 * Вернет модель текущего консультанта (учитывая город)
	 * @return mixed
	 */
	public static function getCurrent()
	{
		return self::where(['city_id' => Cities::getCurrentCity()->id])->first();
	}
}