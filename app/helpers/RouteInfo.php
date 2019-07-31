<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace helpers;

/**
 * Class RouteInfo. Поможет понять, какой сейчас используется контроллер и action.
 */
class RouteInfo
{
    /**
     * Вернет название активного контроллера.
     *
     * @param bool $toLower переводить в нижний регистр.
     *
     * @return string
     */
    public static function controller($toLower = false)
    {
        $routeArray = \Str::parseCallback(\Route::currentRouteAction(), null);
        $controller = strtolower(str_replace('Controller', '', class_basename(head($routeArray))));
        return $toLower ? strtolower($controller) : $controller;
    }
}