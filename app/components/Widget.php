<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace components;

use helpers\Object;
use helpers\Html;

/**
 * Родительский класс всех виджетов.
 *
 * @package components
 */
class Widget extends Component
{
    /**
     * @var array стек открытых виджетов.
     */
    public static $stack = [];

    /**
     * @return string вернет виджет в виде строки.
     */
    public function run()
    {
        return '';
    }

    /**
     * Инициализация виджета.
     */
    public function init()
    {
    }


    /**
     * Рендер view виджета.
     *
     * @param string $viewName
     * @param array $params
     * @return \Illuminate\View\View
     */
    public function render($viewName, $params = [])
    {
        $path = explode('\\', $this->className());
        $widget = array_pop($path);
        $path[] = 'views';
        $path[] = strtolower($widget);
        $path[] = $viewName;

        \View::addLocation(__DIR__ . '/../');

        return \View::make(implode('/', $path), $params);
    }

    /**
     * Render widger.
     *
     * @param array $params
     *
     * @return int
     */
    public static function widget($params = [])
    {
        ob_start();
        ob_implicit_flush(false);
        /* @var $widget Widget */
        $params['class'] = get_called_class();
        $widget = Object::create($params);
        $out = $widget->run();

        return ob_get_clean() . $out;
    }

    /**
     * Открытие виджета.
     *
     * @param array $params
     *
     * @return Widget
     */
    public static function begin($params = [])
    {
        /* @var $widget Widget */
        $params['class'] = get_called_class();
        $widget = Object::create($params);
        static::$stack[] = $widget;
        $widget->init();
        return $widget;
    }

    /**
     * Закрытие и выполнение виджта.
     *
     * @return mixed
     */
    public static function end()
    {
        if (!empty(static::$stack)) {
            $widget = array_pop(static::$stack);
            if (get_class($widget) === get_called_class()) {
                return $widget->run();
            }
        }

        return '';
    }
}