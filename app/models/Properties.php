<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

use components\ActiveRecord;

/**
 * ActiveRecord таблицы `properties`
 *
 * @package models
 */
class Properties extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'properties';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'name'       => 'Название',
            'is_visible' => 'Активна',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required|unique:properties,name'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave()
    {
        if ($this->isNewRecord()) {
            //  Сохраним сортировку для новой категории.
            $this->sortable = max(\DB::table($this->getTable())->max('id'), 1);
        }

        return parent::beforeSave();
    }
}