<?php


namespace modules\main\controllers;

use components\import\ProductsVendorExtImport;


class ImportController extends \modules\main\components\BaseController
{
    /**
     * Импорт остатков поставщиков.
     *
     * @return array|string
     */

    public function postVendor()
    {
        if (!\Sentry::check()) {
            return \Redirect::to('/');
        }
        return ProductsVendorExtImport::run();
    }

}
