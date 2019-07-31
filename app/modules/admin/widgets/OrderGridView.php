<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\admin\widgets;

use widgets\GridView;

/**
 * Order widget.
 *
 * @property string $status статус заказа.
 */
class OrderGridView extends GridView
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->model = \models\Orders::className();

        //  Передаем параметры через flash
        \Session::flash(
            '_OrderParams', json_encode(
                [
                    'status'  => $this->status,
                    'city_id' => $this->city_id
                ]
            )
        );

        return parent::run();
    }
}