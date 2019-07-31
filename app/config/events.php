<?php
return [
    1 =>
        [
            'title' => 'Заявка на обратный звонок',
            'auth' => false,
            'fields' => ['city_id', 'phone', 'type'],
            'sms' => [
                'text' => 'Заказан обратный звонок на номер %s'
            ],
            'email' => [
                'template'=>'callme',
                'subject' => 'Обратный звонок',
            ]
        ],
    2 =>
        [
            'title' => 'Заявка на акт сверки',
            'auth' => true,
            'fields' => ['user_id', 'begin', 'end', 'type', 'city_id'],
            'email' => [
                'template'=>'revise',
                'subject' => 'Запрос акта сверки',
            ]
        ],
];