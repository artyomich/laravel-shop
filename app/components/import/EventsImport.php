<?php

namespace components\import;

use components\BaseImport;
use \models\Events;


/**
 * @package components\import
 */
class CallbackImport extends BaseImport
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
        $callme = Events::where('events.id', '>', \Input::get('id') ? \Input::get('id') : 0)
            ->leftJoin('cities', 'cities.id', '=', 'events.city_id')
            ->select('events.*', 'cities.name as city_name')
            ->orderBy('events.id', 'asc')
            ->get()
            ->toArray();

        return $callme;
    }
}
