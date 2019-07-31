<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

use components\ActiveRecord;
use helpers\ArrayHelper;
use helpers\Html;
use Illuminate\Database\Eloquent\Builder;

/**
 * ActiveRecord таблицы `products`.
 *
 * @package models
 */
class OrderItems extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'order_items';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $primaryKey = ['product_id', 'order_id'];

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }
}