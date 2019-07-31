<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

/**
 * ActiveRecord таблицы `properties`
 *
 * @package models
 */
class ProductsProperties extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'products_properties';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $primaryKey = 'product_id';

    /**
     * @var array
     */
    /*protected $fillable = [
        'brand', 'manufacturer', 'model', 'size', 'diameter_inch', 'diameter_mm', 'width_inch', 'width_mm',
        'series', 'diameter_outside', 'index_speed', 'index_load', 'season', 'layouts_normal', 'spikes', 'image_axis',
        'completeness', 'camera'
    ];*/

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'brand'            => 'Бренд',
            'manufacturer'     => 'Производитель',
            'model'            => 'Модель',
            'size'             => 'Типоразмер',
            'diameter_inch'    => 'Посадочный диаметр, дюймы',
            'diameter_mm'      => 'Посадочный диаметр, мм',
            'width_inch'       => 'Ширина профиля, дюймы',
            'width_mm'         => 'Ширина профиля, мм',
            'series'           => 'Серия профиля, %',
            'diameter_outside' => 'Наружный диаметр, мм.',
            'index_speed'      => 'Индекс скорости',
            'index_load'       => 'Индекс нагрузки',
            'season'           => 'Сезон',
            'layouts_normal'   => 'Норма слойности',
            'spikes'           => 'Шипы',
            'image_axis'       => 'Рисунок\ось',
            'completeness'     => 'Комплектность',
            'camera'           => 'Камера',
            'offset'           => 'Вылет',
            'drilling'         => 'Сверловка',
            'construction'     => 'Тип диска',
            'diameter_inside'  => 'Посадочный диаметр',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id'], 'required']
        ];
    }
}