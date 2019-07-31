<?php
/**
 * @author Maxim Vinnikov <m.vinnikov@yandex.ru>
 */

namespace modules\main\widgets;

use components\Widget;
use models\Cities;
use models\Employers;
use models\Users;

/*
 *Виджет с информацией об авторизованном пользователе в шапке сайта
 */

class HeaderAuth extends Widget
{
    /**
     * @return \Illuminate\View\View|string
     */
    public function run()
    {
        if (\Sentry::check()) {
            $user = \Sentry::getUser();
            $defaultManager = Cities::getCurrentCity();
            $employer = Employers::where('id', $defaultManager->default_manager)->first();
            if (($defaultManager->work_begin < date('H')) and ($defaultManager->work_end > date('H'))) {
                $is_work = 'Офис сейчас открыт';
            } else {
                $is_work = 'Офис сейчас закрыт';
            }
            $param = [
                'is_firm' => $user->is_firm,
                'employer' => $employer,
                'is_work' => $is_work,
            ];
        } else {
            $param = [];
        }
        return $this->render('index', $param);
    }
}