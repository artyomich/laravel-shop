<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace components;

class ConfigWriter extends \Illuminate\Config\FileLoader
{
    public static function set($items, $environment, $group, $namespace = null)
    {
        $instance = new self(
            new \Illuminate\Filesystem\Filesystem(),
            app_path() . '/config'
        );

        $path = $instance->getPath($namespace);

        if (is_null($path)) {
            return;
        }

        $file = (!$environment || ($environment == 'production'))
            ? "{$path}/{$group}.php"
            : "{$path}/{$environment}/{$group}.php";

        //  Загрузка переменных.
        $items = array_merge($instance->files->getRequire($file), $items);

        $instance->files->put($file, '<?php return ' . var_export($items, true) . ';');
    }
}