<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

use components\ActiveRecord;
use components\UploadedFile;
use helpers\ArrayHelper;
use helpers\Html;
use helpers\RouteInfo;

/**
 * ActiveRecord таблицы `banners`
 *
 * @package models
 */
class Events extends \components\ActiveRecord
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
    protected $table = 'events';

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @inheritdoc
     */
    public function rules()
    {

        foreach (\Config::get('events.' . $this->type . '.fields') as $item) {
            $rules[] = [$item, 'required'];
        }
        return $rules;
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