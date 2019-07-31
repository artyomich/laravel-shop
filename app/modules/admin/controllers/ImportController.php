<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\admin\controllers;

use components\import\FirmsImport;
use components\import\ProductsImport;
use components\import\ProductsUpdate;
use components\import\ContractorsImport;
use components\import\ContractsImport;
use components\import\OrdersImport;
use components\import\ProductsVendorIntImport;
use components\import\CallbackImport;
use components\import\ProductsNotificationsImport;
use helpers\ArrayHelper;
use Illuminate\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\DB;
use models\Categories;
use models\Images;
use models\Menus;
use models\MenusTypes;
use models\Pages;
use models\Products;
use models\ProductsBalances;
use models\ProductsImagesRelation;
use models\ProductsProperties;
use models\ProductsPropertiesRelation;
use models\Properties;
use components\UploadedFile;
use models\Visit1C;
use models\YandexMarket;
use modules\main\controllers\SitemapController;
use Whoops\Example\Exception;

/**
 * Контроллер товаров.
 */
class ImportController extends \modules\admin\components\BaseController
{
    /**
     * @var array
     */
    protected $homeLink
        = ['label' => '<i class="glyphicon glyphicon-import"></i> Импорт', 'link' => '/admin/import'];

    /**
     * Главная страница.
     *
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        $this->title = 'Импорт';

        return $this->render('index');
    }

    /**
     * Импорт товаров.
     *
     * @return array|string
     */
    public function postIndex()
    {
        Visit1C::create([]);
        return ProductsImport::run();
    }

    public function postContractsImport()
    {
        return ContractsImport::run();
    }

    public function postContractorsImport()
    {
        return ContractorsImport::run();
    }

    public function postFirms()
    {
        return FirmsImport::run();
    }

    public function postProductsUpdate()
    {
        //Visit1C::create([]);
        return ProductsUpdate::run();
    }

    public function postOrders()
    {
        return OrdersImport::run();
    }

    public function postProducts()
    {
        return ProductsVendorIntImport::run();
    }

    public function getCallback()
    {
        return CallbackImport::run();
    }

    public function getProductsNotifications()
    {
        return ProductsNotificationsImport::run();
    }
}
