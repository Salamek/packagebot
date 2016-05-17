<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Salamek\PackageBot;

class PackageBotPackage
{
    /** @var integer */
    private $id;

    /** @var  int */
    private $orderId;

    /** @var  int */
    private $cashOnDeliveryPrice;

    /** @var  int */
    private $goodsPrice;

    /** @var  array */
    private $services = [];

    /** @var  string */
    private $bankIdentifier;

    /** @var  int */
    private $weight;

    /** @var  integer */
    private $type;

    /** @var  string */
    private $description;

    /** @var null|int */
    private $height = null;

    /** @var null|int */
    private $width = null;

    /** @var null|int */
    private $length = null;

    const DELIVERY_TYPE_STORE = 'DELIVERY_TYPE_STORE';
    const DELIVERY_TYPE_DELIVER = 'DELIVERY_TYPE_DELIVER';


    /**
     * PackageBotPackage constructor.
     * @param $cashOnDeliveryPrice
     * @param $goodsPrice
     * @param string $bankIdentifier
     * @param int $weight
     * @param string $type
     * @param string $description
     */
    public function __construct(
        $orderId,
        $cashOnDeliveryPrice,
        $goodsPrice,
        $bankIdentifier = '',
        $weight = 0,
        $type = self::DELIVERY_TYPE_DELIVER,
        $description = ''
    ) {
        $this->setOrderId($orderId);
        $this->setCashOnDeliveryPrice($cashOnDeliveryPrice);
        $this->setGoodsPrice($goodsPrice);
        $this->setBankIdentifier($bankIdentifier);
        $this->setType($type);
        $this->setWeight($weight);
        $this->setDescription($description);
    }

    /**
     * @param $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param integer $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param $cashOnDeliveryPrice
     */
    public function setCashOnDeliveryPrice($cashOnDeliveryPrice)
    {
        $this->cashOnDeliveryPrice = $cashOnDeliveryPrice;
    }

    /**
     * @param int $goodsPrice
     */
    public function setGoodsPrice($goodsPrice)
    {
        $this->goodsPrice = $goodsPrice;
    }

    /**
     * @param array $services
     */
    public function setServices(array $services)
    {
        $this->services = $services;
    }

    /**
     * @param string $bankIdentifier
     */
    public function setBankIdentifier($bankIdentifier)
    {
        $this->bankIdentifier = $bankIdentifier;
    }

    /**
     * Set weight of package in grams
     * @param int $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @param int $length
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getCashOnDeliveryPrice()
    {
        return $this->cashOnDeliveryPrice;
    }

    /**
     * @return int
     */
    public function getGoodsPrice()
    {
        return $this->goodsPrice;
    }

    /**
     * @return array
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @return string
     */
    public function getBankIdentifier()
    {
        return $this->bankIdentifier;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @return int|null
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @return int|null
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return int|null
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }
}