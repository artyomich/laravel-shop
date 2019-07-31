<?php
/**
 * @author Artyom Arifulin <arifulin@gmail.com>
 */
namespace modules\main\controllers;

use models\Cities;
use models\ProductsNotifications;

class ProductsNotificationsController extends \modules\main\components\BaseController
{
    public function postIndex()
    {
        $model = new ProductsNotifications();
        $data = \Input::get();
        $data['ProductsNotifications']['city_id'] = Cities::getCurrentCity()->id;

        //dd($data);

        if ($model->load($data) && $model->save()) {
            return $this->answerAjax('OK');
        }
        return $this->answerAjax('FAIL');
    }

    public function sendEmail($model)
    {
        if (!\App::environment('local') && !is_null(\Config::get('events.' . $model->type . '.email'))) {
            $city = Cities::getCurrentCity();
            $emails = explode(',', $city->email_manager);
            \Mail::send(
                'emails/' . \Config::get('events.' . $model->type . '.email.template'),
                [
                    'text' => \Config::get('events.' . $model->type . '.email.text'),
                    'data' => $model,
                ],
                function ($message) use ($emails, $model) {
                    foreach ($emails as $email) {
                        $message
                            ->to(trim($email))
                            ->subject(\Config::get('events.' . $model->type . '.email.subject'));
                    }
                });
        }
    }
}