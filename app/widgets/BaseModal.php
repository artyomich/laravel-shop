<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace widgets;

use helpers\ArrayHelper;
use helpers\Html;

/**
 * Виджет модальных окон на сайте, подгружающий их через AJAX.
 * @package widgets
 */
class BaseModal extends \components\Widget
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('index', array_merge($this->_params, [
            'modal' => $this
        ]));
    }

    /**
     * @return string
     */
    public function getId()
    {
        $parts = explode('\\', $this->className());
        return end($parts);
    }

    /**
     * @return string
     */
    public function getButtonName()
    {
        return '';
    }

    /**
     * @param array $attributes
     * @param array $options
     * @return string
     */
    public static function a($attributes = [], $options = [])
    {
        /** @var BaseModal $class */
        $className = get_called_class();
        $class = new $className();
        return Html::a(ArrayHelper::getValue($options, 'buttonName', $class->getButtonName()), '#', array_merge($attributes, [
            'data-toggle' => 'modal',
            'data-modal-preload' => 1,
            'data-target' => '#' . $class->getId()
        ]));
    }

    /**
     * @inheritdoc
     */
    public function render($viewName, $params = [])
    {
        //  Просто убераем modal с конца названия директории.
        $path = explode('\\', $this->className());
        $widget = array_pop($path);
        $path[] = 'views';
        $path[] = strtolower(substr($widget, 0, -5));
        $path[] = $viewName;

        \View::addLocation(__DIR__ . '/../');

        return \View::make(implode('/', $path), $params);
    }
}