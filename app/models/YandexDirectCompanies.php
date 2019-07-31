<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

/**
 * @package models
 */
class YandexDirectCompanies extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'yandex_direct_companies';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name', 'alias'], 'required']
        ];
    }
}