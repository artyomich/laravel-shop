<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace components\yandex;

use components\yandex\models\Offers;

/**
 * Class YandexPartnerApi
 * @package components
 */
class YandexPartnerApi extends \Yandex\Market\Partner\PartnerClient
{
    /**
     * @var bool
     */
    protected $is_debug = false;

    /**
     * @param array $params
     * @return models\GetOffersResponse
     * @throws \Yandex\Common\Exception\ForbiddenException
     * @throws \Yandex\Common\Exception\UnauthorizedException
     * @throws \Yandex\Market\Partner\Exception\PartnerRequestException
     */
    public function getOffersResponse($params = [])
    {
        $resource = 'campaigns/' . $this->campaignId . '/offers.json';
        $resource .= '?' . http_build_query($params);

        $client = new \Guzzle\Service\Client($this->getServiceUrl($resource));
        $request = $client->createRequest('GET');

        $response = $this->sendRequest($request)->json();
        return new models\GetOffersResponse($response);
    }

    /**
     * @param array $params
     * @return models\Offers|null
     */
    public function getOffers($params = [])
    {
        return $this->getOffersResponse($params)->getOffers();
    }

    /**
     * @param array $params
     * @return Offers
     */
    public function getAllOffers($params = [])
    {
        $params = array_merge($params, ['page' => 1, 'pageSize' => $this->is_debug ? 40 : 1000]);
        $response = $this->getOffersResponse($params);
        $pager = $response->getPager();
        $offers = new Offers;

        for ($i = 1; $i <= $pager->getPagesCount(); ++$i) {
            $response = $this->getOffersResponse(array_merge($params, ['page' => $i]));
            $offers->fromArray($response->getOffers());

            if ($this->is_debug && $i >= 5) {
                break;
            }
        }

        return $offers;
    }

    /**
     * @param $is_debug
     */
    public function setDebugMode($is_debug)
    {
        $this->is_debug = $is_debug;
    }
}