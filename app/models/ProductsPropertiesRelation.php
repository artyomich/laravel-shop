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
class ProductsPropertiesRelation extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'products_properties_relation';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $primaryKey = 'product_id';

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
            [['product_id', 'property_id'], 'required']
        ];
    }
}