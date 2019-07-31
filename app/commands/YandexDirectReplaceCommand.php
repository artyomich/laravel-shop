<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

use Illuminate\Console\Command;
use models\Orders;

class YandexDirectReplaceCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'command:yandexdirectreplace';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * Заменит идентификаторы кампаний в заказах на человекопонятные назания.
     *
     * @return mixed
     */
    public function fire()
    {
        $orders = Orders::whereRaw('direct_ad_id ~ \'^[0-9]\'')->get();
        $adIds = \helpers\ArrayHelper::getColumn($orders, 'direct_ad_id');
        if (!count($adIds)) {
            echo "Нет данных на обработку!\n";
            return;
        }

        $ads = $this->getAdGroups($adIds);

        /** @var \models\Orders $order */
        foreach ($ads as $ad) {
            $order = Orders::where('direct_ad_id', '=', $ad->AdGroupId)->first();
            if (!$order) {
                continue;
            }

            $order->direct_ad_id = \helpers\ArrayHelper::getValue($ad, 'TextAd.Title', null);
            $order->save();
        }
    }

    /**
     * Вернет список объявлений.
     *
     * @param array $ids идентификаторы объявлений.
     * @return mixed
     */
    private function getAdGroups(Array $ids)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, 'https://api.direct.yandex.com/json/v5/ads');
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . \Config::get('yandex-api.direct.access_token'),
            'Accept-Language: ru'
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode([
            'method' => 'get',
            'params' => [
                'SelectionCriteria' => [
                    'AdGroupIds' => $ids,
                    'Types' => ['TEXT_AD']
                ],
                'FieldNames' => [
                    'AdGroupId'
                ],
                'TextAdFieldNames' => [
                    'Title'
                ]
            ]
        ]));
        $out = json_decode(curl_exec($curl));
        curl_close($curl);

        if (isset($out->error)) {
            throw new Exception($out->error);
        }

        return \helpers\ArrayHelper::getValue($out, 'result.Ads', []);
    }
}
