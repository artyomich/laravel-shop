<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace components\yandex\models;

use Yandex\Market\Models\Pager;
use Yandex\Market\Models\Orders;
use Yandex\Common\Model;

class GetOffersResponse extends Model
{
    protected $pager = null;

    protected $offers = null;

    protected $mappingClasses = array(
        'pager' => 'Yandex\Market\Models\Pager',
        'offers' => 'components\yandex\models\Offers'
    );

    protected $propNameMap = array();

    /**
     * Retrieve the pager property
     *
     * @return Pager|null
     */
    public function getPager()
    {
        return $this->pager;
    }

    /**
     * Set the pager property
     *
     * @param Pager $pager
     * @return $this
     */
    public function setPager($pager)
    {
        $this->pager = $pager;
        return $this;
    }

    /**
     * Retrieve the offers property
     *
     * @return Offers|null
     */
    public function getOffers()
    {
        return $this->offers;
    }

    /**
     * Set the offers property
     *
     * @param Offers $orders
     * @return $this
     */
    public function setOffers($offers)
    {
        $this->offers = $offers;
        return $this;
    }
}
