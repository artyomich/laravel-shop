<?php

namespace modules\admin\controllers;

use \models\Markup;

class MarkupController extends \modules\admin\components\BaseController
{
    /**
     * @var array
     */
    protected $homeLink
        = ['label' => '<i class="glyphicon glyphicon-piggy-bank"></i> Наценка', 'link' => '/admin/markup'];

    public function anyIndex()
    {
        $this->title = 'Наценка';
        $this->breadcrumbs[] = $this->title;
        $model = Markup::find(1);
        if (\Request::isMethod('POST')) {
            $markup=\Input::get('Markup');
            $model->value = $markup['value'];
            $model->save();
        }
        return $this->render('index', ['model' => $model]);
    }

}