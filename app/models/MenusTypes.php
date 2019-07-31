<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

/**
 * ActiveRecord таблицы `menus_types`
 *
 * @package models
 */
class MenusTypes extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'menus_types';

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
            ['name', 'required'],
            ['alias', 'required|max:16|unique:menus_types,name']
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        //  TODO: Подчищаем меню.
        parent::afterDelete();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'    => 'ID',
            'name'  => 'Название',
            'alias' => 'Псевдоним'
        ];
    }
}