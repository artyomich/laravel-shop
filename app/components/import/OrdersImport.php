<?php
/**
 * @author Dmitriy Koshelev
 */
namespace components\import;

use components\BaseImport;
use models\Orders;


/**
 * Родительский класс для всех импортов.
 *
 * @package components\import
 */
class OrdersImport extends BaseImport
{
    /**
     * @var bool
     */
    protected $enableExport = false;

    /**
     * @inheritdoc
     */
    public static function run($className = self::class)
    {
        return parent::run($className);
    }

    /**
     * @inheritdoc
     */
    protected function import($data)
    {
        if (isset($data->import['item'])) {
            $items = isset($data->import['item'][0]) ? $data->import['item'] : [$data->import['item']];
            foreach ($items as $k => $itemData) {
                $order= Orders::find($itemData['id']);
                if ($order != null) {
                    $order->status= array_flip ($order['statuses'])[$itemData['status']];
                    $order->save();
                }
            }
        }
    }
}