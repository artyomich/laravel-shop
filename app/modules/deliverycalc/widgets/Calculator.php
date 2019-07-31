<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\deliverycalc\widgets;

use components\Widget;
use models\Cities;
use models\Categories;
use modules\deliverycalc\models\TyresWeight;

class Calculator extends Widget
{

    public function run()
    {
        $currentCity = Cities::getCurrentCity();
        switch ($this->product->categories->type) {
            case Categories::TYPE_TIRES:
                (!isset($this->product->properties->weight) OR !$this->product->properties->weight)
                AND $this->product->properties->weight = TyresWeight::weight($this->product->properties);
                break;
            case Categories::TYPE_DISKS:
                $this->product->properties->weight = 10;
                $this->product->properties->diameter_outside = 0.4;
                $this->product->properties->width_mm = 0.4;
                break;
        }
        return $this->render('index', [
            'cities' => $currentCity->stateCities,
            'current_city' => $currentCity->name,
            'current_cdek_id' => $currentCity->cdek_id,
            'product' => $this->product
        ]);
    }
}
