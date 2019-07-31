<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

/**
 * ActiveRecord таблицы `products_images_relation`
 *
 * @package models
 */
class ProductsImagesRelation extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'products_images_relation';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $primaryKey = ['product_id', 'image_id'];

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Название'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'image_id'], 'required']
        ];
    }

    /**
     * Вернет модель изображения.
     */
    public function image()
    {
        return $this->hasOne(Images::className(), 'id', 'image_id');
    }

    /**
     * Вернет модель товара.
     */
    public function product()
    {
        return $this->hasOne(Images::className(), 'id', 'product_id');
    }
}
