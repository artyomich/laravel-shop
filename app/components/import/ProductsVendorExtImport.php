<?php

namespace components\import;

use components\BaseImport;
use models\ProductsBalances;

/**
 * @package components\import
 */
class ProductsVendorExtImport extends BaseImport
{
    /**
     * @var bool
     */
    protected $enable1CVisitLog = false;

    protected $enableExport = false;

    protected $isClearZip = false;

    /**
     * @inheritdoc
     */
    public static function run($className = self::class)
    {
        parent::run($className);
        return \Redirect::to('/user/vendor')->with(['message' => 'Сохранено']);
    }

    /**
     * @inheritdoc
     */
    protected function import($data)
    {
        $this->copyFileVendor();
        $data = (array)$data;
        $data = array_shift($data);
        ProductsBalances::clearBalanceVendor(\Sentry::getUser()->id);
        ProductsBalances::addingBalanceVendor($data);
    }

    /**
     * @Копирует файлы полученные от поставщика и отправляет на почту в 1С
     */
    static function copyFileVendor()
    {
        $user = \Sentry::getUser();
        $path = '/tmp/import_componentsimportProductsVendorExtImport';
        $path_storage = storage_path() . '/vendor/';
        $files = \File::files($path);
        foreach ($files as $key => $value) {
            if (\File::exists($value) and \File::extension($value) === 'zip') {
                if (!\File::exists($path_storage)) {
                    \File::makeDirectory($path_storage, 0777);
                }
                $path_storage = $path_storage . $user->id . '_' . date('YmdHis') . '.zip';
                \File::copy($value, $path_storage);
            }
            unlink($value);
        }
        $emailMessage = '';
        \Mail::send(
            'emails/1c-import-vendor-list',
            [
                'text' => $emailMessage
            ],
            function ($message) use ($path_storage) {
                $message
                    ->to(\Config::get('mail.addresses.1cImportVendorList'))
                    ->subject('Каталог поставщика')
                    ->attach($path_storage);
            }
        );
    }
}