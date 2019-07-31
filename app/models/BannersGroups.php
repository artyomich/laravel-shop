<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

/**
 * ActiveRecord таблицы `users`
 *
 * @package models
 */
class BannersGroups extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'banners_groups';

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
            ['name', 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'   => 'ID',
            'name' => 'Название'
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        $banners = Banners::where('group_id', $this->id)->get();
        foreach ($banners as $banner) {
            $banner->group_id = null;
            $banner->save();
        }
    }
}