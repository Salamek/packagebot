<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Salamek\PackageBot\Model;


use Nette\SmartObject;
use Salamek\PackageBot\Enum\Currency;
use Salamek\PackageBot\Exception\WrongDeliveryDataException;

class PaymentInfo
{
    use SmartObject;
    
    /** @var int */
    private $cashOnDeliveryPrice;

    /** @var string */
    private $cashOnDeliveryCurrency;

    /** @var string */
    private $bankIdentifier;

    /**
     * PackageBotPaymentInfo constructor.
     * @param $cashOnDeliveryPrice
     * @param $cashOnDeliveryCurrency
     * @param $bankIdentifier
     */
    public function __construct($cashOnDeliveryPrice, $cashOnDeliveryCurrency, $bankIdentifier)
    {
        $this->setCashOnDeliveryPrice($cashOnDeliveryPrice);
        $this->setCashOnDeliveryCurrency($cashOnDeliveryCurrency);
        $this->setBankIdentifier($bankIdentifier);
    }

    /**
     * @param $cashOnDeliveryPrice
     */
    public function setCashOnDeliveryPrice($cashOnDeliveryPrice)
    {
        $this->cashOnDeliveryPrice = $cashOnDeliveryPrice;
    }

    /**
     * @param $cashOnDeliveryCurrency
     * @throws WrongDeliveryDataException
     */
    public function setCashOnDeliveryCurrency($cashOnDeliveryCurrency)
    {
        if (!in_array($cashOnDeliveryCurrency, Currency::$list))
        {
            throw new WrongDeliveryDataException('Unsupported currency code, supported codes are '.implode(', ', Currency::$list));
        }
        
        $this->cashOnDeliveryCurrency = $cashOnDeliveryCurrency;
    }

    /**
     * @param string $bankIdentifier
     */
    public function setBankIdentifier($bankIdentifier)
    {
        $this->bankIdentifier = $bankIdentifier;
    }

    /**
     * @return int
     */
    public function getCashOnDeliveryPrice()
    {
        return $this->cashOnDeliveryPrice;
    }

    /**
     * @return string
     */
    public function getCashOnDeliveryCurrency()
    {
        return $this->cashOnDeliveryCurrency;
    }

    /**
     * @return string
     */
    public function getBankIdentifier()
    {
        return $this->bankIdentifier;
    }
}