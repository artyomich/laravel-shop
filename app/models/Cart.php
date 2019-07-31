<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

/**
 * Модель корзины.
 *
 * @package models
 */
class Cart
{
    /**
     * Вернет позиции в корзине.
     *
     * @param array $ids идентификаторы нужны, чтобы принудительно установить значения в корзине,
     *                   если например покупатель вышел покурить  вернулся, с сессия уже завершилась.
     *
     * @return array|mixed
     */
    public static function getItems($ids = null)
    {
        $cart = json_decode(\Session::get('cart', '{}'), true);
        if (!isset($cart['items'])) {
            $cart = [
                'items' => [],
                'total' => 0,
                'count' => 0,
            ];
        }
        if (!isset($cart['total'])) {
            $cart['total'] = 0;
            $cart['count'] = 0;
        }

        //  Принудительно задаем значения для корзины.
        if (is_array($ids)) {
            $cart['items'] = [];
            $cart['count'] = 0;
            foreach ($ids as $key => $value) {
                $cart['items'][$key] = ['count' => $value];
            }
        }
        $ids = array_keys($cart['items']);
        if (count($ids)) {
            !isset($cart['count']) ? $cart['count'] = 0 : '';
            $products = Products::with('balances')->whereIn('id', $ids)->get();
            foreach ($cart['items'] as $k => &$item) {
                foreach ($products as $product) {
                    $product->calcDeliveryCost();
                    if ($product->id == $k) {
                        $item['model'] = $product;
                        if (isset($item['vendor'])) {
                            foreach ($item['vendor'] as $key => &$itemVendor) {
                                if ($product->calcDeliveryCost()->vendorsBalances) {
                                    foreach ($product->calcDeliveryCost()->vendorsBalances as $vendorsBalances) {
                                        if ($vendorsBalances["vendor_id"] === $key) {
                                            $itemVendor['costMarkUp'] = isset($vendorsBalances["costMarkUp"]) ? $vendorsBalances["costMarkUp"] : 0;
                                            $itemVendor['delivery'] = isset($vendorsBalances["delivery"]) ? $vendorsBalances["delivery"] : 0;
                                            $itemVendor['deliveryPeriodMin'] = isset($vendorsBalances["deliveryPeriodMin"]) ? $vendorsBalances["deliveryPeriodMin"] : 0;
                                            $itemVendor['deliveryPeriodMax'] = isset($vendorsBalances["deliveryPeriodMax"]) ? $vendorsBalances["deliveryPeriodMax"] : 0;
                                            $itemVendor['balance'] = isset($vendorsBalances["balance"]) ? $vendorsBalances["balance"] : 0;
                                            $cart['count']++;
                                            $cart['total'] = $cart['total'] + $itemVendor['costMarkUp'] * $itemVendor['count'] + $itemVendor['delivery'];
                                        }
                                    }
                                }
                            }
                        }
                        if (isset($item['count'])) {
                            $cost = isset($product->getBalances(\Cookie::get('city_id', Cities::CITY_BY_DEFAULT))->cost) ? $product->getBalances(\Cookie::get('city_id', Cities::CITY_BY_DEFAULT))->cost : 0;
                            $cart['count']++;
                            $cart['total'] = $cart['total'] + $cost * $item['count'];
                        }
                        break;
                    }
                }
            }
        }
        return $cart;
    }

    /**
     * Добавит товар в корзину.
     *
     * @param integer $productId
     * @param integer $count
     *
     * @return boolean
     */
    public static function add($productId, $count, $vendorId = null)
    {
        $product = Products::find($productId);
        $balance = $product->getBalances(\Cookie::get('city_id', Cities::CITY_BY_DEFAULT), $vendorId)->balance_full;
        $cart = json_decode(\Session::get('cart', '{}'), true);
        //  Проверка наличия продукта в базе.
        if (!$product) {
            return false;
        }

        if (!isset($cart['items'])) {
            $cart = [
                'items' => [],
                'total' => 0
            ];
        }
        if (isset($cart['items'][$productId]['vendor'][$vendorId]['count']) && $vendorId) {
            $cart['items'][$productId]['vendor'][$vendorId]['count'] += $count;
            if ($cart['items'][$productId]['vendor'][$vendorId]['count'] > $balance)
                $cart['items'][$productId]['vendor'][$vendorId]['count'] = $balance;
        } elseif (isset($cart['items'][$productId]['count']) && is_null($vendorId)) {
            $cart['items'][$productId]['count'] += $count;
            if ($cart['items'][$productId]['count'] > $balance)
                $cart['items'][$productId]['count'] = $balance;
        } elseif ($vendorId) {
            $cart['items'][$productId]['vendor'][$vendorId]['count'] = $count;
        } else {
            $cart['items'][$productId]['count'] = $count;
        }

        \Session::set('cart', json_encode($cart));
        return true;
    }

    /**
     * Пересчитает количество товара в корзине.
     * Если количество == 0, тогда позиция будет удалена.
     *
     * @param integer $productId
     * @param array $count
     */
    public static function update($productId, $count)
    {
        $cart = json_decode(\Session::get('cart', '{}'), true);

        if (!isset($cart['items'])) {
            $cart = [
                'items' => [],
                'total' => 0
            ];
        }

        //  Если количество меньше, то вообще удалим позицию.
        /*if ($count <= 0) {
            unset($cart['items'][$productId]);
        }
        else {*/
        //}
        foreach ($count as $key => $value) {
            if ($key === 0) {
                $cart['items'][$productId]['count'] = $value;
            } else {
                $cart['items'][$productId]['vendor'][$key]['count'] = $value;
            }
        }

        \Session::set('cart', json_encode($cart));
    }

    /**
     * Удалет позицию из корзины.
     */
    public static function remove($productId, $vendorId)
    {
        $cart = json_decode(\Session::get('cart', '{}'), true);

        if (!isset($cart['items'])) {
            $cart = [
                'items' => [],
                'total' => 0
            ];
        }

        //  Если количество меньше, то вообще удалим позицию.
        if (is_null($vendorId)) {
            if (isset($cart['items'][$productId]) and !isset($cart['items'][$productId]['vendor'])) {
                unset($cart['items'][$productId]);
            } elseif (isset($cart['items'][$productId]['vendor'])) {
                unset($cart['items'][$productId]['count']);
            }
        } else {
            if (isset($cart['items'][$productId]['vendor'][$vendorId])) {
                if (count($cart['items'][$productId]['vendor']) === 1) {
                    unset($cart['items'][$productId]['vendor']);
                };
                unset($cart['items'][$productId]['vendor'][$vendorId]);
            }
        }

        \Session::set('cart', json_encode($cart));
    }

    /**
     * Удалит все позиции из корзины.
     */
    public static function clear()
    {
        $cart = json_decode(\Session::get('cart', '{}'), true);
        $cart['items'] = [];
        \Session::set('cart', json_encode($cart));
    }
}