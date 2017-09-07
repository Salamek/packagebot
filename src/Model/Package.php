<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Salamek\PackageBot\Model;

use Salamek\PackageBot\Enum\TransportService;

class Package
{
    /** @var SeriesNumberInfo */
    private $seriesNumberInfo;

    /** @var int */
    private $orderId;

    /** @var int */
    private $goodsPrice;

    /** @var array */
    private $services = [];

    /** @var integer */
    private $transportService;

    /** @var string */
    private $description;

    /** @var Recipient */
    private $recipient;

    /** @var PaymentInfo */
    private $paymentInfo;

    /** @var WeightedPackageInfo */
    private $weightedPackageInfo;

    /** @var integer */
    private $packageCount;

    /** @var integer */
    private $packagePosition;

    /** @var null|SeriesNumberInfo */
    private $parentSeriesNumberInfo = null;

    /** @var array */
    private $specialData = [];

    /**
     * PackageBotPackage constructor.
     * @param $orderId
     * @param int $goodsPrice
     * @param int $type
     * @param string $description
     * @param Recipient $recipient
     * @param null|PaymentInfo $paymentInfo
     * @param null|WeightedPackageInfo $weightedPackageInfo
     * @param integer $packageCount
     * @param integer $packagePosition
     * @param null|SeriesNumberInfo $parentSeriesNumberInfo
     */
    public function __construct(
        $orderId,
        $goodsPrice = 0,
        $type = TransportService::CZECH_POST_PACKAGE_TO_HAND,
        $description = '',
        Recipient $recipient,
        PaymentInfo $paymentInfo = null,
        WeightedPackageInfo $weightedPackageInfo = null,
        $packageCount = 1,
        $packagePosition = 1,
        $parentSeriesNumberInfo = null,
        $specialData = []
    ) {
        $this->setOrderId($orderId);
        $this->setGoodsPrice($goodsPrice);
        $this->setTransportService($type);
        $this->setDescription($description);
        $this->setRecipient($recipient);
        $this->setPaymentInfo($paymentInfo);
        $this->setWeightedPackageInfo($weightedPackageInfo);
        $this->setPackageCount($packageCount);
        $this->setPackagePosition($packagePosition);
        $this->setParentSeriesNumberInfo($parentSeriesNumberInfo);
    }

    /**
     * @param $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }


    /**
     * @param int $goodsPrice
     */
    public function setGoodsPrice($goodsPrice)
    {
        $this->goodsPrice = $goodsPrice;
    }

    /**
     * @param SeriesNumberInfo $seriesNumberInfo
     */
    public function setSeriesNumberInfo($seriesNumberInfo)
    {
        $this->seriesNumberInfo = $seriesNumberInfo;
    }

    /**
     * @param integer $transportService
     */
    public function setTransportService($transportService)
    {
        $this->transportService = $transportService;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param array $services
     */
    public function setServices(array $services)
    {
        $this->services = $services;
    }

    /**
     * @param Recipient $recipient
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
    }

    /**
     * @param null|PaymentInfo $paymentInfo
     */
    public function setPaymentInfo(PaymentInfo $paymentInfo = null)
    {
        $this->paymentInfo = $paymentInfo;
    }

    /**
     * @param null|WeightedPackageInfo $weightedPackageInfo
     */
    public function setWeightedPackageInfo(WeightedPackageInfo $weightedPackageInfo = null)
    {
        $this->weightedPackageInfo = $weightedPackageInfo;
    }

    /**
     * @param int $packageCount
     */
    public function setPackageCount($packageCount)
    {
        $this->packageCount = $packageCount;
    }

    /**
     * @param int $packagePosition
     */
    public function setPackagePosition($packagePosition)
    {
        $this->packagePosition = $packagePosition;
    }

    /**
     * @param null|SeriesNumberInfo $parentSeriesNumberInfo
     */
    public function setParentSeriesNumberInfo($parentSeriesNumberInfo)
    {
        $this->parentSeriesNumberInfo = $parentSeriesNumberInfo;
    }

    /**
     * @return SeriesNumberInfo
     */
    public function getSeriesNumberInfo()
    {
        return $this->seriesNumberInfo;
    }

    /**
     * @return int
     */
    public function getTransportService()
    {
        return $this->transportService;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return int
     */
    public function getGoodsPrice()
    {
        return $this->goodsPrice;
    }

    /**
     * @return Recipient
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @return PaymentInfo
     */
    public function getPaymentInfo()
    {
        return $this->paymentInfo;
    }

    /**
     * @return WeightedPackageInfo
     */
    public function getWeightedPackageInfo()
    {
        return $this->weightedPackageInfo;
    }

    /**
     * @return int
     */
    public function getPackageCount()
    {
        return $this->packageCount;
    }

    /**
     * @return int
     */
    public function getPackagePosition()
    {
        return $this->packagePosition;
    }

    /**
     * @return null|SeriesNumberInfo
     */
    public function getParentSeriesNumberInfo()
    {
        return $this->parentSeriesNumberInfo;
    }

    /**
     * @param $key
     * @return mixed
     * @throws \Exception
     */
    public function getSpecialData($key)
    {
        if (!array_key_exists($key, $this->specialData))
        {
            throw new \Exception('Key not found in special data');
        }

        return $this->specialData[$key];
    }

    /**
     * @param $key
     * @param $data
     */
    public function setSpecialData($key, $data)
    {
        $this->specialData[$key] = $data;
    }
}