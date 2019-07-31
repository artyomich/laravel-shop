<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace components\yandex\models;

use Yandex\Common\ObjectModel;

class Offers extends ObjectModel
{
    protected $collection = array();

    protected $mappingClasses = array();

    protected $propNameMap = array();

    /**
     * Add item
     */
    public function add($offer)
    {
        if (is_array($offer)) {
            $this->collection[] = new Offer($offer);
        } elseif (is_object($offer) && $offer instanceof Offer) {
            $this->collection[] = $offer;
        }

        return $this;
    }

    /**
     * @return Offer[]
     */
    public function getAll()
    {
        return $this->collection;
    }
}
