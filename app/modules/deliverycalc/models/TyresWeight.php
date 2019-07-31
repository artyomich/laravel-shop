<?php

namespace modules\deliverycalc\models;

/**
 * ActiveRecord таблицы `cities_states_delivery`
 *
 * @package models
 */
class TyresWeight extends \components\ActiveRecord {

    /**
     * @var string название таблицы.
     */
    protected $table = 'tyres_weight';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['width', 'profile', 'diameter', 'volume', 'weight'], 'required']
        ];
    }

    public static function weight($props) {
        $width = str_replace(',', '.', $props->width_mm) OR $width = str_replace(',', '.', $props->width_inch) * 25;
        $profile = str_replace(',', '.', $props->series) OR $profile = 82;
        $diameter = str_replace(',', '.', $props->diameter_inch) OR $diameter = str_replace(',', '.', $props->diameter_mm) / 25;
        $tyre = TyresWeight::where('width', '<=', floor($width))
                ->where('profile', '<=', floor($profile))
                ->where('diameter', '<=', $diameter)
                ->orderBy('width', 'DESC')
                ->orderBy('profile', 'DESC')
                ->orderBy('diameter', 'DESC')
                ->first();
        return isset($tyre->weight) ? $tyre->weight : 5;
    }

}
