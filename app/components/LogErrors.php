<?php
/**
 * Класс предназначен для отправки СМС.
 *
 * @version 1.6
 */

namespace components;

use models\LogErrors;

/**
 * Class Sms
 *
 * @package helpers
 */
class LogErrorsComponent
{
    /**
     * @param $code
     * @param string $message
     */
    public static function log($code, $message = '')
    {
        if (
            php_sapi_name() == 'cli' OR
            ((int)$code === 404 AND (!isset($_SERVER['HTTP_REFERER']) OR !$_SERVER['HTTP_REFERER']))
        ) {
            return;
        }

        //  Добавим в справочник.
        $model = new LogErrors;
        $model->setAttributes([
            'url' => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '',
            'method' => isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '',
            'code' => $code,
            'referer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
            'remote_url' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
            'message' => $message
        ]);
        $model->save();
    }
}

;
?>