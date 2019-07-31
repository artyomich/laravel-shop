<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace models;

/**
 * Модель для отправки ссобщений с сайта.
 *
 * @package models
 */
class Support extends \components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email', 'message'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => 'Ваше имя',
            'email' => 'Email',
            'message' => 'Сообщение'
        ];
    }

    /**
     * Заглушка.
     * @param array $options
     */
    public function save(array $options = [])
    {
    }

    /**
     * Отправка сообщения.
     */
    public function sendMessage()
    {
        $city = Cities::getCurrentCity();

        \Mail::send(
            'emails/support', [
            'text' => $this->message,
            'from' => $this->email,
            'username' => $this->username
        ], function ($message) use ($city) {
            $message
                ->to($city->email)
                ->subject('Сообщение с сайта');
        });
    }
}