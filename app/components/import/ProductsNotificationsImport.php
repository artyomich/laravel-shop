<?php
/**
 * @author Artyom Arifulin <arifulin@gmail.com>
 */

namespace components\import;

use components\BaseImport;
use models\ProductsNotifications;


/**
 * @package components\import
 */
class ProductsNotificationsImport extends BaseImport
{
    /**
     * @var bool
     */

    protected $enableExport = true;

    protected $enableImport = false;


    /**
     * @inheritdoc
     */
    public static function run($className = self::class)
    {
        return parent::run($className);

    }

    protected function import($data)
    {

    }

    protected function export($importResult = [])
    {
        $notifications = ProductsNotifications::where('products_notifications.id', '>', \Input::get('id') ? \Input::get('id') : 0)
            ->leftJoin('cities', 'cities.id', '=', 'products_notifications.city_id')
            ->select('products_notifications.*', 'cities.name as city_name')
            ->orderBy('products_notifications.id', 'asc')
            ->get()
            ->toArray();

        return $notifications;
    }
}

