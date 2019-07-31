<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\deliverycalc\models;

/**
 * ActiveRecord таблицы `cities_states_delivery`
 *
 * @package models
 */
class CitiesStatesDelivery extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'cities_states_delivery';

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
            [['city_id', 'name'], 'required']
        ];
    }
}