<?php
/**
 * @author Maxim Vinnikov <m.vinnikov@yuandex.ru>
 */

namespace modules\main\controllers;


class RecommendController extends \modules\main\components\BaseController
{
    public function postIndex()
    {
        //Отправка сообщения с рекомендацией по улучшению страницы
        $data['comment'] = \Input::get('comment', false);
        $data['cityName'] = \Input::get('cityName', 'Не определенно');
        $data['email'] = \Input::get('email', 'Не предоставлен');
        $data['pageLink'] = \Input::get('pageLink', 'Не определенно');

        if ($data['comment']) {
            \Mail::send(
                'emails/dev-recommend',
                $data,
                function ($message) {
                    $message
                    ->to('user1152@ashk.ru')
                    ->subject('Новое предложение по улучшению сайта poshk.ru');
            });
            return $this->answerAjax('OK');
        } else {
            return $this->answerAjax('FAIL');
        }
    }
}