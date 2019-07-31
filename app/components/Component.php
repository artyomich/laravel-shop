<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace components;

/**
 * Расширяет класс добавляя геттеры, сеттеры и дополнительные вспомогательные методы.
 */
class Component
{
    /**
     * @var array параметры объекта.
     */
    protected $_params = [];

    /**
     * Геттер параметров виджета.
     *
     * @param string $name название параметра.
     *
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->_params[$name]) ? $this->_params[$name] : null;
    }

    /**
     * Сеттер параметров виджета.
     *
     * @param string $name название параметра.
     *
     * @return mixed
     */
    public function __set($name, $value)
    {
        $this->_params[$name] = $value;
    }

    /**
     * Component constructor.
     * @__construct
     * @param array $params
     */
    public function __construct($params = [])
    {
        foreach ($params as $name => $value) {
            $this->$name = $value;
        }
    }

    /**
     * @return string вернет название вызванного класса.
     */
    public static function className()
    {
        return get_called_class();
    }
}