<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace widgets;

use components\Widget;
use components\ActiveRecord;

/**
 * Photos widget.
 *
 * @property string       $template
 * @property ActiveRecord $model
 */
class Photos extends Widget
{
    /**
     * @var string
     */
    public $template = 'pages';

    /**
     * @var ActiveRecord
     */
    public $model;

    /**
     * @inheritdoc
     */
    public function run()
    {
        //  Шаблон для рендера.
        if (!isset($this->template)) {
            \App::abort(500, 'Не задан шаблон для рендера фотографий.');
        }

        if (!isset($this->model)) {
            \App::abort(500, 'Не задана модель для фотографий.');
        }

        return $this->render($this->template, ['model' => $this->model]);
    }
}