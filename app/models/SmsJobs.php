<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

/**
 * ActiveRecord таблицы `sms_jobs`
 *
 * @package models
 */
class SmsJobs extends \components\ActiveRecord
{
    protected $table = 'sms_jobs';

    /**
     * @var bool
     */
    public $timestamps = false;
}