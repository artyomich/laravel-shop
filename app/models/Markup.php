<?php

namespace models;

/**
 * ActiveRecord таблицы `markup`.
 *
 * @package models
 */
class Markup extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'markup';
    public $timestamps = false;
}