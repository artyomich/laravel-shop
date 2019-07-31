<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

use helpers\Html;

/**
 * ActiveRecord таблицы `users_groups`
 *
 * @package models
 */
class UsersGroupsRelation extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'users_groups';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $primaryKey = ['user_id', 'group_id'];

    /**
     * @var bool
     */
    public $incrementing = false;
}