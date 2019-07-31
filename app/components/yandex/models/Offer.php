<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace components\yandex\models;

use Yandex\Common\Model;

class Offer extends Model
{
    /**
     * @var int
     */
    protected $id = null;

    /**
     * @var int
     */
    protected $feedId = null;

    /**
     * @var int
     */
    protected $modelId = null;

    /**
     * @var int
     */
    protected $price = null;

    /**
     * @var string
     */
    protected $currency = null;

    /**
     * @var int
     */
    protected $bid = null;

    /**
     * @var int
     */
    protected $cbid = null;

    /**
     * @var int
     */
    protected $url = null;

    /**
     * @var int
     */
    protected $shopCategoryId = null;

    /**
     * @var int
     */
    protected $marketCategoryId = null;

    /**
     * @var string
     */
    protected $name = null;

    /**
     * @var array
     */
    protected $mappingClasses = array();

    /**
     * @var array
     */
    protected $propNameMap = array();

    /**
     * Retrieve the id property
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the id property
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getModelId()
    {
        return $this->modelId;
    }

    /**
     * @param int $modelId
     * @return $this
     */
    public function setModelId($modelId)
    {
        $this->modelId = $modelId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getFeedId()
    {
        return $this->feedId;
    }

    /**
     * @param int $feedId
     * @return $this
     */
    public function setFeedId($feedId)
    {
        $this->feedId = $feedId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getShopCategoryId()
    {
        return $this->shopCategoryId;
    }

    /**
     * @param int $shopCategoryId
     * @return $this
     */
    public function setShopCategoryId($shopCategoryId)
    {
        $this->shopCategoryId = $shopCategoryId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMarketCategoryId()
    {
        return $this->marketCategoryId;
    }

    /**
     * @param int $shopCategoryId
     * @return $this
     */
    public function setMarketCategoryId($marketCategoryId)
    {
        $this->marketCategoryId = $marketCategoryId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param int $price
     * @return $this
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}
