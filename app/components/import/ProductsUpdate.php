<?php
/**
 * @author Artyom Arifulin <arifulin@gmail.com>
 */

namespace components\import;

use components\ActiveRecord;
use components\BaseImport;
use helpers\ArrayHelper;
use models\Products;
use models\ProductsBalances;
use models\YandexMarket;


/**
 * @package components\import
 */
class ProductsUpdate extends BaseImport
{
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
        $this->_cities = \models\Cities::all();
        $errors = [];
        $ids1C = [];            //  id продуктов из 1с, которые были импортированы с ошибками.
        $importedIDS1C = [];
        $progress = 0;

        foreach ($data->import as $itemData) {
            $product = Products::where('id_1c', '=', $itemData['Код'])->first();
            if (isset($product)) {
                //  Сохранение цен и остатков.
                $status = $this->saveBalances($product, $itemData);
                if (!$product || $status !== true) {
                    $errors[] = print_r($status, true);
                    $ids1C[] = $itemData['Код'];
                    $product->delete();
                    continue;
                };
            } else {
                $errors[] = 'Товар с id_1c = ' . $itemData['Код'] . ' не был найден, возможно это новый товар.';
                continue;
            }

            \Cache::flush();

            unset($product);

            $importedIDS1C[] = $itemData['Код'];

            if (!$this->is1C()) {
                //  Прогресс.
                if (!ob_start("ob_gzhandler")) {
                    ob_start();
                }

                ++$progress;
                echo '[' . $progress . '] Импортировано ' . $itemData['Код'] . '<br/>';
                ob_get_contents();

                ob_end_flush();

                echo str_repeat(' ', 65536);
            }

        }

        \Cache::flush();

        //	Обновление файлов для выгрузку в маркет.
        YandexMarket::updateXml();

        //  Последний штрих - делаем невидимыми все, что не были импортированы.
        $products = Products::whereNotIn('id_1c', $importedIDS1C);

        //  Обнуление остатков.
        ProductsBalances::whereIn('product_id', ArrayHelper::getColumn($products->get(), 'id'))
            ->update([
                'balance' => 0,
                'balance_full' => 0,
                'cost' => 0,
                'cost_opt_small' => 0,
                'cost_opt_middle' => 0,
                'cost_opt_big' => 0,
                'cost_spec' => 0,
                'cost_min' => 0,
                'cost_retail' => 0
            ]);

        if (count($errors)) {
            $emailMessage = '<strong>Данные обновлены с ошибками :(</strong><br>' .
                'Обновлено: ' . count($importedIDS1C) . '<br>' .
                'Ошибки: ' . count($errors) . '<br>' .
                '1C IDS: ' . implode(', ', $ids1C) . '<br>' .
                'Дополнительная информация для отдела разработки:<br><small>' .
                implode('<br>', is_array($errors) ? $errors : []) . '</small>';
        } else {
            $emailMessage = '<strong>Данные импортированы успешно!</strong><br>' .
                'Импортировано: ' . count($importedIDS1C);
        }

        if (!\App::environment('local')) {
            \Mail::send(
                'emails/1c-import',
                [
                    'text' => $emailMessage
                ],
                function ($message) {
                    $message
                        ->to(\Config::get('mail.addresses.1cImport'))
                        ->subject('Отчет о выполнении скрипта апдейта данных');
                }
            );
        }

        if (!$this->is1C()) {
            \Session::flash(count($errors) ? 'error' : 'success', $emailMessage);
            return \Redirect::to('/admin/import/');
        } else {
            return 'Не импортировано: ' . count($errors);
        }
    }

    /**
     * Сохраненит остатки и стоимости шин в разных городах.
     *
     * @param ActiveRecord $product
     * @param array $data
     *
     * @return bool
     */
    public function saveBalances(&$product, $data)
    {
        try {
            $product_balances = ProductsBalances::whereNull('vendor_id')->where('product_id', '=', $product->id);
            $product_balances->delete();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $queriesData = [];

        foreach ($this->_cities as $city) {
            $cost = isset($data['Цена ' . $city->name]) ? (int)str_replace(' ', '', $data['Цена ' . $city->name]) : 0;
            $cost_opt_small = isset($data['Цена мелк.опт ' . $city->name]) ? (int)str_replace(' ', '', $data['Цена мелк.опт ' . $city->name]) : 0;
            $cost_opt_middle = isset($data['Цена средн.опт ' . $city->name]) ? (int)str_replace(' ', '', $data['Цена средн.опт ' . $city->name]) : 0;
            $cost_opt_big = isset($data['Цена крупн.опт ' . $city->name]) ? (int)str_replace(' ', '', $data['Цена крупн.опт ' . $city->name]) : 0;
            $cost_spec = isset($data['Цена спец. ' . $city->name]) ? (int)str_replace(' ', '', $data['Цена спец. ' . $city->name]) : 0;
            $cost_min = isset($data['Цена мин. ' . $city->name]) ? (int)str_replace(' ', '', $data['Цена мин. ' . $city->name]) : 0;
            $cost_retail = isset($data['Цена розничная ' . $city->name]) ? (int)str_replace(' ', '', $data['Цена розничная ' . $city->name]) : 0;

            $balance = isset($data['Остатки свободные ' . $city->name]) ? (int)$data['Остатки свободные ' . $city->name] : 0;
            $balance_full = isset($data['Остатки ' . $city->name]) ? (int)$data['Остатки ' . $city->name] : 0;

            $isSpec = (isset($data['Акция ' . $city->name]) && !empty($data['Акция ' . $city->name])) ? 't' : 'f';

            if ($cost) {
                $queriesData[] = "(" . $city->id . ", " . $product->id . ", $cost, $cost_opt_small, $cost_opt_middle, $cost_opt_big, $cost_spec, $cost_min, $cost_retail, $balance, $balance_full, '$isSpec')";
            }

            unset($cost);
            unset($cost_opt_small);
            unset($cost_opt_middle);
            unset($cost_opt_big);
            unset($cost_spec);
            unset($cost_min);
            unset($cost_retail);
            unset($balance);
            unset($balance_full);
        }

        if (!count($queriesData)) {
            $product->delete();
        } else {
            \DB::statement(
                \DB::raw(
                    "INSERT INTO products_balances (city_id, product_id, cost, cost_opt_small, cost_opt_middle, cost_opt_big, cost_spec, cost_min, cost_retail, balance, balance_full, is_spec_cost)" .
                    "VALUES" . implode(', ', $queriesData) . ";"
                )
            );
        }

        unset($queriesData);

        return true;
    }
}