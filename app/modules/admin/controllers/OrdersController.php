<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\admin\controllers;

use components\ActiveRecord;
use components\ConfigWriter;
use helpers\ArrayHelper;
use helpers\CsvToArray;
use models\Categories;
use models\OrdersSearch;
use models\Products;
use models\Visit1C;
use Whoops\Example\Exception;
use SoapBox\Formatter\Formatter;

/**
 * Контроллер категорий.
 */
class OrdersController extends \modules\admin\components\BaseController
{
    /**
     * Экспорт грида в excel
     */
    const EXPORT_EXCEL = 1;

    /**
     * Экспорт заказов с доставкой.
     */
    const EXPORT_EXCEL_WITH_DELIVERY = 2;

    /**
     * @var array домашняя ссылка для хлебных крошек.
     */
    protected $homeLink = ['label' => '<i class="fa fa-shopping-cart"></i> Заказы', 'link' => '/admin/orders/'];

    /**
     * Главная страница со списком заказов.
     *
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        $this->title = 'Заказы';
        $this->breadcrumbs[] = $this->title;

        $user = \Sentry::getUser();
        $groups = $user->getGroups();

        switch (\Input::get('excel')) {
            case self::EXPORT_EXCEL:
                return $this->saveToExcel();
                break;

            case self::EXPORT_EXCEL_WITH_DELIVERY:
                return $this->saveToExcelWithDelivery();
                break;
        }

        return $this->render($groups[0]->alias == 'managers' ? 'index' : 'index-admin');
    }

    /**
     * Обновление
     *
     * @param integer $id
     */
    public function anyUpdate($id)
    {
        $model = $this->loadModel($id);
        $user = \Sentry::getUser();

        $this->title = 'Заказ №' . $model->id;
        $this->breadcrumbs[] = $this->title;
        $viewParams = [
            'model' => $model,
            'isDisabled' => $model->manager_id && $user->id != $model->manager_id
        ];

        //  Занзачим менеджером, если еще не назначили.
        if (!isset($model->manager_id)) {
            \Session::flash('success', 'Заказ был назначен на вас!');
            $model->manager_id = \Sentry::getUser()->id;
            $model->save();
        }

        if (\Request::isMethod('post')) {
            $input = \Input::all();
            $totalCost = 0;

            $model->load($input);

            //  Считаем общую стоимость.
            foreach ($model->items as $item) {
                /** @var ActiveRecord $orderItem */
                $orderItem = \models\OrderItems::where('order_id', $model->id)->where('product_id', $item->id)->first();
                if (!isset($input['amount'][$item->id]) || $input['amount'][$item->id] < 1) {
                    $orderItem->delete();
                    continue;
                }

                $orderItem->amount = (int)$input['amount'][$item->id];
                $orderItem->save();
                //  Тут можно обращаться к $orderItem->amount, но он вренет старые данные >_<
                $totalCost += ((int)$input['amount'][$item->id] * $item->cost);
            }

            $model->cost = round($totalCost * ((100 - (int)$input['Orders']['discount']) / 100));

            if ($model->save()) {
                return $this->redirect('/admin/orders/update/' . $model->id . '/');
            }
        }

        return \Request::ajax()
            ? $this->renderAjax('form', $viewParams)
            : $this->render('form', $viewParams);
    }

    /**
     * Добавит товар в заказ.
     *
     * @param $orderId
     */
    public function postAdditem()
    {
        $input = \Input::all();
        if (!isset($input['oid']) || !isset($input['pid'])) {
            return $this->errorAjax('Данные не были переданы');
        }

        /** @var \models\Products $product */
        $product = \models\Products::where('id_1c', $input['pid'])->first();
        if (!$product) {
            return $this->errorAjax('Товар не найден');
        }

        /** @var \models\Orders $order */
        $order = \models\Orders::find($input['oid']);
        if (!$order) {
            return $this->errorAjax('Такого заказа не существует');
        }

        //  Проверка наличия шин в этом городе.
        $balance = $product->getBalanceByCityId($order->city_id);
        if (!$balance || $balance->balance < 1 || $balance->cost < 1) {
            return $this->errorAjax('В этом городе нет таких шин');
        }

        $items = \models\OrderItems::where('order_id', $order->id)->where('product_id', $product->id)->get()->first();
        if (!$items) {
            //  Если не существует, тогда добавляем.
            $item = new \models\OrderItems;
            $item->setAttributes(
                [
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'amount' => 4,
                    'cost' => $balance->cost,
                    'status' => 'N' //  TODO: remove from database
                ]
            );

            if (!$item->save()) {
                return $this->errorAjax('Не удалось добавить товар');
            }
        }

        return $this->renderAjax(
            'ajax-addItem', [
            'model' => \models\Orders::with('items')->find($input['oid'])
        ],
            true
        );
    }

    /**
     * Экспорт списка заказов в 1С
     * @param int $from
     */
    public function anyExport()
    {
        $result = [];
        $model = new OrdersSearch;
        $search = $model->search(\Input::all())
            ->where('orders.id', '>=', \Input::get('from', 0));
        foreach ($search->with(['items', 'city'])->get() as $order) {
            if (isset($order->id_user) and $order->id_user) {
                $itemuser=\Sentry::findUserById($order->id_user);
                $order->inn=$itemuser->inn;
                $order->kpp=$itemuser->kpp;
            } else{
                $order->inn='';
                $order->kpp='';
            }
            $result[] = $order->toArray();
        }
        $formatter = Formatter::make($result, Formatter::ARR);

        //  Запишем дату последнего обращения и уравняем версии падений (см. алгоритм отправки смс).
        \components\ConfigWriter::set([
            'lastVisit' => time(),
            'lastFailVersion' => \Config::get('1c.lastFailSms')
        ], '', '1c');

        //  Пишем в базу
        Visit1C::create([]);

        echo $formatter->toXml();
    }

    /**
     * Сохранит данные из грида в excel.
     */
    private function saveToExcel()
    {
        //  Сохраняем в Excel если необходимо
        $model = new OrdersSearch;
        $search = $model->search(\Input::all());

        //$columns = (new \models\Orders)->defaultColumns();
        //$columns = array_merge(['id', 'user_name'], $columns);
        $columns = ['id', 'status', 'cost', 'user_name', 'email', 'phone', 'city_id', 'date_create', 'is_from_direct', 'direct_campaign', 'direct_ad_id', 'is_paid'];
        $cities = ArrayHelper::map(\models\Cities::all(), 'id', 'name');


        \Excel::create(
            'Export', function ($excel) use ($model, $search, $cities, $columns) {
            $excel->sheet(
                'Лист1', function ($sheet) use ($model, $search, $cities, $columns) {
                $header = [];
                foreach ($columns as $col) {
                    $header[] = $model->getAttributeLabel($col);
                }
                $header[] = 'Номенклатура';
                $sheet->appendRow($header);

                foreach ($search->get() as $item) {
                    $orderItems = [];
                    foreach ($item->items()->get() as $orderItem) {
                        $orderItems[] = $orderItem->name . ' (' . $orderItem->amount . 'шт)';
                    }
                    $isPaid = $item->columnIsPaid('да');
                    $item = $item->toArray();
                    $item['status'] = $model->statuses[$item['status']];
                    $item['city_id'] = $cities[$item['city_id']];
                    $item['is_paid'] = $isPaid;
                    $data = array_only($item, $columns);
                    $data[] = implode(', ', $orderItems);
                    $sheet->appendRow($data);


                }
            }
            );
        }
        )->download('xlsx');

        return \URL::previous();
    }

    /**
     * Сохранит данные из грида в excel заказы, у которых есть доставка.
     */
    private function saveToExcelWithDelivery()
    {
        //  Сохраняем в Excel если необходимо
        $model = new OrdersSearch;
        $search = $model->search(\Input::all());
        $total = $search->get()->count();
        $search = $search->whereNotNull('address');

        $columns = ['id', 'city_id', 'date_create', 'address', 'cost'];
        $cities = ArrayHelper::map(\models\Cities::all(), 'id', 'name');

        \Excel::create(
            'Export', function ($excel) use ($model, $search, $cities, $columns, $total) {
            $excel->sheet(
                'Лист1',
                function ($sheet) use ($model, $search, $cities, $columns, $total) {
                    $header = [];
                    foreach ($columns as $col) {
                        $header[] = $model->getAttributeLabel($col);
                    }
                    $sheet->appendRow([
                        'Общее число заказов',
                        $total
                    ]);
                    $sheet->appendRow($header);

                    foreach ($search->get() as $item) {
                        $items = $item->toArray();
                        $data = [];
                        foreach ($columns as $col) {
                            $data[$col] = $items[$col];
                        }

                        $data['city_id'] = $cities[$data['city_id']];

                        //$item['city_id'] = $cities[$item['city_id']];
                        //$data = array_only($item, $columns);
                        $sheet->appendRow($data);
                    }
                }
            );
        })->download('xlsx');

        return \URL::previous();
    }
}