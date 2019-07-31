<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace components\yandex;

/**
 * Class YandexContentApi
 * @package components
 */
class YandexContentApi
{
    /**
     * @param $r
     */
    public function request($r, $isJson = true)
    {
        $headers = array(
            "Host: api.content.market.yandex.ru",
            "Accept: */*",
            "Authorization: " . \Config::get('yandexmarket.content.access_token')
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, 'https://api.content.market.yandex.ru/v1/' . $r . ($isJson ? '.json' : ''));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $data = curl_exec($ch);
        curl_close($ch);

        try {
            return json_decode($data);
        } catch (\Exception $e) {
            return '';
        }
    }
}