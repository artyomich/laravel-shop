<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\main\controllers;

use components\ActiveRecord;
use components\Sms;
use models\Cart;
use models\Cities;
use models\OnlinePay;
use models\Orders;
use models\Products;
use models\Users;
use models\UsersContracts;

/**
 * Контроллер корзины.
 */
class CartController extends \modules\main\components\BaseController
{
    /**
     * Главная страница со списком товаров.
     *
     * @return \Illuminate\View\View
     */
    public function anyIndex()
    {
        $this->title = 'Корзина';
        $items = Cart::getItems();
        $lastOrder = Orders::orderBy('id', 'DESC')->first();
        if (\Sentry::check()) {
            $user = \Sentry::getUser();
            $user_data=[
                'email' => $user->email,
                'phone' => $user->phone,
                'last_name' => $user->last_name,
                'first_name' => $user->first_name,
                'is_firm' => $user->is_firm,
                'firm' => $user->firm,
                'address' => $user->address,
                'inn' => $user->inn,
                'ogrn' => $user->ogrn,
                'kpp' => $user->kpp,
                'rs' => $user->rs,
                'ks' => $user->ks,
                'bik' => $user->bik,
                'bank' => $user->bank,
            ];
        }

        $viewData = [
            'model' => new Orders,
            'cart' => $items,
            'pays' => OnlinePay::getMethods(),
            'countOrders' => isset($lastOrder) ? $lastOrder->id : 0,
            'current_cdek_id' => Cities::getCurrentCity()->cdek_id,
            'user' => \Sentry::check() ? $user_data : '',
        ];

        if (\Request::isMethod('post') && \Input::get('make_order')) {

            $input = \Input::except('_token');

            /** @var ActiveRecord $viewData ['model'] */
            $formName = $viewData['model']->formName();
            if (\Sentry::check()) {
                $input[$formName]['id_user'] = $user->id;
            }
            if ($input['delivery'] == 'warehouse') {
                $input[$formName]['address'] = '';
            }
            if ($input['onlinepay']) {
                $input[$formName]['onlinepay'] = $input['onlinepay'];
            }
            $input[$formName]['cost'] = $items['total'];
            $viewData['model']->orderItems = $items['items'];

            //  Добавим флаг, что пользователь был привлечен из маркета\директа.
            if (\Cookie::has('is_from_direct')) {
                $viewData['model']->is_from_direct = 'D';
            } elseif (\Cookie::has('is_from_market')) {
                $viewData['model']->is_from_direct = 'M';
            } elseif (\Cookie::has('is_from_adwords')) {
                $viewData['model']->is_from_direct = 'A';
            }

            //  Фраза из поисковой системы.
            if (\Cookie::has('direct_compaign') || \Cookie::has('direct_ad_id')) {
                $viewData['model']->direct_campaign = \Cookie::get('direct_campaign', null);
                $viewData['model']->direct_ad_id = \Cookie::get('direct_ad_id', null);
            }

            \Sentry::getUser() and $contract = UsersContracts::where('user_id', \Sentry::getUser()->id)->where('city_id', Cities::getCurrentCity()->id)?UsersContracts::where('user_id', \Sentry::getUser()->id)->where('city_id', Cities::getCurrentCity()->id)->first():'';
            if (isset($contract->id_1c))
                $input[$formName]['contract_1c_id'] = $contract->id_1c;

            if (!$viewData['model']->load($input) || !$viewData['model']->save()) {
                $viewData['model']->reload();
                return $this->render('index', $viewData);
            }


            //  Очищаем корзину.
            Cart::clear();

            //  Отправляем письмо пользователю.
            if (!\App::environment('local')) {

                if (!empty($viewData['model']->email)) {
                    \Mail::send(
                        'emails/make-order', [
                        'order' => $viewData['model']
                    ], function ($message) use ($viewData) {
                        $message
                            ->to($viewData['model']->email)
                            ->subject('Заказ №' . $viewData['model']->id);
                    });
                }

                //  Отправляем СМС клиенту.
                \components\Sms::send(
                    '+7' . $viewData['model']->phone,
                    'Добрый день ' . $viewData['model']->user_name .
                    '. Ваш заказ №' . $viewData['model']->id . ' принят в обработку. Резерв составляет 3 дня.'
                );

                //  Отправляем смс менеджерам.
                $city = Cities::getCurrentCity();
                $phones = explode(',', $city->phone_manager);

                $orderList = '';
                foreach ($viewData['model']->items as $item) {
                    $orderList .= $item->name . ', ' . $item->amount . 'шт. ' . $item->amount * $item->cost . ' руб.' . ($item->vendor_id == 0 ? '' : '+ доствка ' . Products::getCdek($item->id, $item->vendor_id, $city)->priceByCurrency . ' руб.')
                        . "\n";
                }

                $message = 'Заказ №' . $viewData['model']->id . '.' . "\n" .
                    "Состав заказа:\n" . $orderList . "\n" .
                    "Город: " . $city->name . "\n" .
                    'Клиент ' . $viewData['model']->user_name . ' Телефон +7' . $viewData['model']->phone;

                foreach ($phones as $phone) {
                    Sms::addJob($phone, $message, [$city->work_begin, $city->work_end]);
                }

                //  Письмо менеджерам.
                $emails = explode(',', $city->email_manager);
                $message .= "\nEmail: " . $viewData['model']->email;
                $message = str_replace("\n", "<br/>\n", $message);

                \Mail::send(
                    'emails/make-order-managers', [
                    'text' => $message,
                    'ip'=> $_SERVER['REMOTE_ADDR']
                ], function ($message) use ($emails, $viewData) {
                    foreach ($emails as $email) {
                        $message
                            ->to(trim($email))
                            ->subject('Заказ №' . $viewData['model']->id);
                    }
                });
            }

            return \Redirect::to(OnlinePay::getPayUrlByAlias($input['onlinepay'], $viewData['model']->id));
        }
        $viewData["cart"] = $items;
        $template = 'index' . ((\Sentry::check() && \Sentry::getUser()->is_firm) ? '_partner' : '');
        return !isset($items['items']) || empty($items['items'])
            ? $this->redirect('/')
            : $this->render($template, $viewData);
    }

    /**
     * Обновит данные корзины.
     */
    public function postUpdate()
    {
        $items = \Input::get('count');
        $cart = Cart::getItems();

        if (count($items)) {
            foreach ($cart['items'] as $id => &$item) {
                Cart::update($id, isset($items[$id]) ? $items[$id] : 0);
            }
        } else {
            Cart::clear();
        }

        return $this->answerAjax(\modules\main\widgets\Cart::widget());
    }

    /**
     * Добавит товар в корзину.
     */
    public function anyAdd($id)
    {
        $status = \models\Cart::add($id, \Input::get('count'), \Input::get('vendor') ? \Input::get('vendor') : null);
        if (\Request::isMethod('post')) {
            return $status
                ? $this->answerAjax(\modules\main\widgets\Cart::widget())
                : $this->errorAjax('Не удалось добавить товар в корзину :(');
        }

        return $this->redirect(\URL::previous());
    }

    /**
     * Удалит товар из корзины.
     */
    public function anyRemove($id, $vendorId = null)
    {
        \models\Cart::remove($id, $vendorId);
        return \Request::isMethod('post')
            ? $this->answerAjax(\modules\main\widgets\Cart::widget())
            : $this->redirect(\URL::previous());
    }
}
