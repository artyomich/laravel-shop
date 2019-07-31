<?php

namespace models;

/**
 * ActiveRecord таблицы `users_contracts`.
 *
 * @package models
 */
class UsersContracts extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'users_contracts';
    /**
     * @const
     */
    const CREATED_AT = 'created_at';

    /**
     * @const
     */
    const UPDATED_AT = 'updated_at';

    protected $fillable = ['name', 'user_id', 'delay_type', 'id_1c', 'cost_type', 'city_id'];

    public $timestamps = true;

    /**
     * Определение процента взависимости от срока отсрочки.
     *
     */

    public function delays_percent()
    {
        return ContractsDelays::whereRaw($this->type->delay . ' BETWEEN "min" AND "max"')->first()->value;

    }

    /**
     * Получение контрактов пользователя
     *
     */

    static public function getFirstContracts()
    {
        return UsersContracts::where('user_id', \Sentry::getUser()->id)->where('city_id', Cities::getCurrentCity()->id)->first();

    }

    /**
     * Получение названия типа цены пользователя
     *
     */
    static public function getFirstCostType()
    {
        return UsersContracts::getFirstContracts() ? UsersContracts::getFirstContracts()->name : 'Мелкий опт';

    }
}