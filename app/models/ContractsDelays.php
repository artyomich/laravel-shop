<?php

namespace models;

/**
 * ActiveRecord таблицы `contracts_delays`.
 *
 * @package models
 */
class ContractsDelays extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'contracts_delays';
    /**
     * @const
     */
    const CREATED_AT = 'created_at';

    /**
     * @const
     */
    const UPDATED_AT = 'updated_at';


    public $timestamps = true;

    protected $primaryKey = 'min';
}