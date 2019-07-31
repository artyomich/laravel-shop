<?php
/**
 * @author Artyom Arifulin <arifulin@gmail.com>
 */

namespace models;

use components\ActiveRecord;
use components\UploadedFile;
use helpers\ArrayHelper;
use helpers\Html;
use helpers\RouteInfo;

/**
 * ActiveRecord таблицы `products_notifications`
 *
 * @package models
 */
class ProductsNotifications extends \components\ActiveRecord
{
    /**
     * @const
     */
    const CREATED_AT = 'created_at';

    /**
     * @const
     */
    const UPDATED_AT = 'updated_at';

    /**
     * @var string название таблицы.
     */
    protected $table = 'products_notifications';

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var string
     */
    protected $primaryKey = ['id'];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'city_id'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        ];
    }

    /**
     * Связь с таблицей `cities`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo('\models\Cities', 'city_id');
    }

    /**
     * Вернет название города.
     *
     * @return string
     */
    public function getCityName()
    {
        return $this->city ? $this->city->name : '';
    }
}
