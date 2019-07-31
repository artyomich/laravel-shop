<?php
/**
 * @author Dmitriy Koshelev <kde2707@mail.ru>
 */

namespace modules\main\widgets;

use components\Widget;

/**
 * Modal widget.
 */
class Modal extends Widget
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('index');
    }
}