<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\main\widgets;

use components\Widget;

/**
 * Main widget.
 *
 * @property string $type
 * @property string $template
 */
class Categories extends Widget
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render(
            'index', [
                'categories'    => \models\Categories::getAll(),
                'categoryAlias' => isset($this->_params['categoryAlias']) ? $this->_params['categoryAlias'] : ''
            ]
        );
    }
}