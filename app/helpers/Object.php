<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace helpers;

/**
 * GridView
 */
class Object
{
    /**
     * @__construct
     */
    public static function create($params = [])
    {
        if (!isset($params['class'])) {
            \App::abort(500, 'Class must be set');
        }

        $class = $params['class'];
        unset($params['class']);

        return new $class($params);
    }
}