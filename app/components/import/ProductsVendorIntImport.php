<?php

namespace components\import;

use components\BaseImport;
use models\ProductsBalances;

/**
 * @package components\import
 */
class ProductsVendorIntImport extends BaseImport
{
    /**
     * @var bool
     */
    protected $enable1CVisitLog = false;

    protected $enableExport = false;

    protected $isClearZip = true;

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
        ProductsBalances::updateBalanceVendor($data->import);
    }
}