<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\main\controllers;

use components\ActiveRecord;
use components\Sms;
use Liebig\Cron\Facades\Cron;
use models\Cart;
use models\Cities;
use models\OnlinePay;
use models\OrderItems;
use models\Orders;
use models\SmsJobs;

/**
 * Контроллер заказа.
 */
class OrderController extends \modules\main\components\BaseController
{
    /**
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        return $this->redirect('/');
    }

    /**
     * Оформление нового заказа.
     */
    public function anyMake()
    {
        $this->title = 'Оформление заказа';
        $items = Cart::getItems(\Input::get('count'));

		//	Нечего делать в оформлении заказа с пустой корзиной.
		if (!count($items['items'])) {
			return \Redirect::to('/');
		}

        $viewData = [
            'model' => new Orders,
			'cart' => $items,
			'pays' => OnlinePay::getMethods()
        ];

        if (\Request::isMethod('post') && \Input::get('make_order')) {
            $input = \Input::all();
            /** @var ActiveRecord $viewData ['model'] */
            $formName = $viewData['model']->formName();
            if ($input['delivery'] == 'warehouse') {
                $input[$formName]['address'] = '';
            }
            $input[$formName]['cost'] = $items['total'];
            $viewData['model']->orderItems = $items['items'];

            if (!$viewData['model']->load($input) || !$viewData['model']->save()) {
                $viewData['model']->reload();
                return $this->render('make', $viewData, false);
            }

            //  Очищаем корзину.
            Cart::clear();

            //  Отправляем письмо пользователю.
			if (!\App::environment('local')) {
				\Mail::send(
					'emails/make-order', [
					'order' => $viewData['model']
				], function ($message) use ($viewData) {
					$message
						->to($viewData['model']->email)
						->subject('Заказ №' . $viewData['model']->id);
				});

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
					$orderList .= $item->name . ', ' . $item->amount . 'шт. ' . $item->amount * $item->cost . ' руб.'
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
					'text' => $message
				], function ($message) use ($emails, $viewData) {
					foreach ($emails as $email) {
						$message
							->to(trim($email))
							->subject('Заказ №' . $viewData['model']->id);
					}
				}
				);

			}

			return \Redirect::to(OnlinePay::getPayUrlByAlias($input['onlinepay'], $viewData['model']->id));
        }

        return $this->render('make', $viewData);
    }

    /**
     * Страница "Благодарим за покупку".
     *
     * @param integer $id
     */
    public function getThanks($id)
    {
        return $this->render('thanks', ['model' => Orders::find($id)]);
    }

    /**
     * Вернет состояние заказа.
     *
     * @param string $hash
     */
    public function getState($hash)
    {
        $model = Orders::where('code', $hash)->first();
        if (!isset($model)) {
            \App::abort(404, 'Информация о заказе не найдена.');
        }

        return $this->render('state', ['model' => $model]);
    }

    /**
     * @return string|ActiveRecord вернет полное имя модели по умолчанию для этого контроллера.
     */
    public static function modelName()
    {
        return '\\models\\Orders';
    }
}
