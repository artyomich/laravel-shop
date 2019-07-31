<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

/**
 * Модель логов 1С.
 *
 * @package models
 */
class Visit1C extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'visit_1c';
}