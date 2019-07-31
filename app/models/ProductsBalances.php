<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;


/**
 * ActiveRecord таблицы `products_balances`
 *
 * @package models
 */
class ProductsBalances extends \components\ActiveRecord
{
    /**
     * @var string название таблицы.
     */
    protected $table = 'products_balances';

    /**
     * @var array
     */
    protected $fillable = array('product_id', 'city_id', 'vendor_id', 'cost', 'balance', 'product_vendor_id', 'is_spec_cost');

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $primaryKey = 'product_id';

    /**
     * @var array
     */
    //protected $fillable = ['product_id', 'city_id', 'cost', 'balance'];

    /**
     * @return string вернет количество товара в формате: 1, есть или много.
     */
    public function getBalanceFormat()
    {
        if ($this->balance > 1 && $this->balance < 10) {
            return 'есть';
        } elseif ($this->balance > 10) {
            return 'много';
        } elseif ($this->balance == 0) {
            return 'нет';
        } else {
            return $this->balance . ' шт.';
        }
    }

    /**
     * То же самое, что и getBalanceFormat, только статическое.
     *
     * @param integer $balance
     *
     * @return string
     */
    public static function formatBalance($balance)
    {
        if ($balance > 1 && $balance < 10) {
            return $balance . ' шт.';
        } elseif ($balance > 10) {
            return 'много';
        } elseif ($balance == 0) {
            return 'нет';
        } else {
            return $balance . ' шт.';
        }
    }

    /**
     * Определение типа цены для авторизованнго пользователя
     *
     */

    public static function getCostFields()
    {
        if (\Sentry::check() and \Sentry::getUser()->is_firm) {
            $contract = UsersContracts::getFirstContracts();
            $cost_type = $contract ? $contract->cost_type : 'opt_small';
//            $contract->delay_percent();
            $cost = [\DB::raw('COALESCE(NULLIF(cost_' . $cost_type . ',0), products_balances.cost) as cost'), 'products_balances.cost as cost0'];
        } else {
            $cost = [];
        }
//        $cost = (\Sentry::check() and \Sentry::getUser()->is_firm) ? [\DB::raw('COALESCE(NULLIF(cost_opt_small,0), products_balances.cost) as cost'), 'products_balances.cost as cost0'] : [];

        return $cost;
    }

    /**
     * Удаление записей по конкретному постащику
     *
     * @param integer $id
     *
     */
    public static function clearBalanceVendor($id)
    {
        ProductsBalances::where('vendor_id', $id)->delete();
    }


    /**
     * Добавление остатков и цен поставщиков
     *
     * @param array $data
     *
     */
    public static function addingBalanceVendor($data)
    {
        foreach ($data as $key => $value) {
            $products = Products::where('id_1c', $value['Код'])->first();
            if ($value['Цена'] and $value['Остатки']) {
                ProductsBalances::create([
                    'product_id' => $products ? $products->id : 0,
                    'city_id' => null,
                    'cost' => $value['Цена'],
                    'balance' => $value['Остатки'],
                    'vendor_id' => \Sentry::getUser()->id,
                    'product_vendor_id' => $value['Код контрагента'],
                ]);
            }
        }
    }

    /**
     * Обновление остатков и цен поставщиков
     *
     * @param array $data
     *
     */
    public static function updateBalanceVendor($data)
    {
        foreach ($data as $value) {
            $product = Products::where('id_1c', $value['Код продукта'])->first();
            ProductsBalances::where('product_vendor_id', $value['Код продукта поставщика'])
                ->where('vendor_id', $value['Код поставщика'])
                ->update(['product_id' => $product->id, 'cost' => $value['Цена']]);
        }
    }
}