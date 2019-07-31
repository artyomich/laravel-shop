<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace modules\main\controllers;

use components\Sms;
use models\Cities;
use models\Events;

class EventsController extends \modules\main\components\BaseController
{
    public function postIndex()
    {

        $model = new Events();
        $data = \Input::get();
        if (\Sentry::check())
            $data['Events']['user_id'] = \Sentry::getUser()->id;
        $data['Events']['city_id'] = Cities::getCurrentCity()->id;
        $diffs = array_diff_key($data['Events'], array_flip(\Config::get('events.' . $data['Events']['type'] . '.fields')));
        foreach ($data['Events'] as $keyData => $item) {
            foreach ($diffs as $key => $diff) {
                if ($key === $keyData)
                    unset($data['Events'][$keyData]);

            }
        }
        if ($model->load($data) && $model->save()) {
            $this->sendSms($model);
            $this->sendEmail($model);
            return $this->answerAjax('OK');
        }
        return $this->answerAjax('FAIL');
    }

    public function sendSms($model)
    {
        if (!\App::environment('local') && !is_null(\Config::get('events.' . $model->type . '.sms'))) {
            $city = Cities::getCurrentCity();
            $phones = explode(',', $city->phone_manager);
            foreach ($phones as $phone) {
                Sms::addJob($phone,
                    vsprintf(\Config::get('events.' . $model->type . '.sms.text'), $model->phone),
                    [$city->work_begin, $city->work_end]);
            }
        }
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