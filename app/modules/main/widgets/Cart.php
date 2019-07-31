<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\main\widgets;

use components\Widget;

/**
 * Banners widget.
 */
class Cart extends Widget
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $cart = \models\Cart::getItems();
        return $this->render(
            'index', [
                'cart'  => $cart,
                'count' => isset($cart['count']) ? $cart['count'] : null,
            ]
        );
    }
}