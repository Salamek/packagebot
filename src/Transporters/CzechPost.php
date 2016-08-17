<?php
namespace Salamek\PackageBot\Transporters;

use Salamek\CzechPostApi;
use Salamek\CzechPostPackage;
use Salamek\CzechPostPackageWrongDataException;
use Salamek\CzechPostSender;
use Salamek\PackageBot\Enum\Attribute\LabelAttr;
use Salamek\PackageBot\Enum\TransportService;
use Salamek\PackageBot\IPackageBotStorage;
use Salamek\PackageBot\PackageBotPackage;
use Salamek\PackageBot\PackageBotPaymentInfo;
use Salamek\PackageBot\PackageBotReceiver;
use Salamek\PackageBot\WrongDeliveryDataException;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
class CzechPost implements ITransporter
{
    /** @var mixed */
    private $id;

    /** @var mixed */
    private $username;

    /** @var mixed */
    private $password;

    /** @var IPackageBotStorage */
    private $botStorage;

    /** @var CzechPostApi */
    private $api;

    /**
     * CzechPost constructor.
     * @param array $configuration
     * @param array $sender
     * @param IPackageBotStorage $botStorage
     * @param $cookieJar
     */
    public function __construct(array $configuration, array $sender, IPackageBotStorage $botStorage, $cookieJar)
    {
        $this->id = $configuration['id'];
        $this->username = $configuration['username'];
        $this->password = $configuration['password'];
        $this->botStorage = $botStorage;

        $czechPostSender = new CzechPostSender($this->id, $sender['name'], $sender['www'], $sender['street'], $sender['streetNumber'], $sender['zipCode'], $sender['cityPart'], $sender['city'],
            $configuration['postOfficeZipCode']);

        $this->api = new CzechPostApi($this->username, $this->password, $czechPostSender, new CzechPostStorage($this->botStorage), $cookieJar);
    }

    /**
     * @param PackageBotPackage $package
     * @param PackageBotReceiver $receiver
     * @param PackageBotPaymentInfo $paymentInfo
     * @return mixed
     * @throws WrongDeliveryDataException
     * @throws \Exception
     */
    public function doParcel(PackageBotPackage $package, PackageBotReceiver $receiver, PackageBotPaymentInfo $paymentInfo = null)
    {
        $deliveryType = [
            TransportService::DELIVER => CzechPostPackage::DELIVERY_TYPE_DELIVER,
            TransportService::STORE => CzechPostPackage::DELIVERY_TYPE_STORE
        ];

        if (!is_null($paymentInfo))
        {
            $cashOnDeliveryPrice = $paymentInfo->getCashOnDeliveryPrice();
            $bankIdentifier = $paymentInfo->getBankIdentifier();
        }
        else
        {
            $cashOnDeliveryPrice = 0;
            $bankIdentifier = '';
        }

        try {
            $czechPostPackage = new CzechPostPackage($receiver->getCompany(), $receiver->getFirstName(), $receiver->getLastName(), $receiver->getEmail(), $receiver->getPhone(), $receiver->getWww(),
                $receiver->getStreet(), $receiver->getStreetNumber(), $receiver->getZipCode(), $receiver->getCity(), $receiver->getCityPart(),
                $receiver->getState(), $cashOnDeliveryPrice, $package->getGoodsPrice(), [], $bankIdentifier, $package->getWeight(), $deliveryType[$package->getType()],
                $package->getDescription());

            $czechPostPackage->setWidth($package->getWidth());
            $czechPostPackage->setHeight($package->getHeight());
            $czechPostPackage->setLength($package->getLength());
            $czechPostPackage->setOrderId($package->getOrderId());
        } catch (CzechPostPackageWrongDataException $e) {
            throw new WrongDeliveryDataException($e->getMessage());
        }

        $this->api->persistPackage($czechPostPackage);

        return $this->api->generatePackageIdentifier($czechPostPackage);
    }

    /**
     * @return void
     */
    public function doFlush()
    {
        $this->api->flushPackages();
    }

    /**
     * @param $id
     * @param $decomposition
     * @return string
     * @throws \Exception
     */
    public function doGenerateLabel($id, $decomposition)
    {
        switch ($decomposition)
        {
            case LabelAttr::DECOMPOSITION_QUARTER:
                $decompositionCzechPost = CzechPostApi::LABEL_DECOMPOSITION_QUARTER;
                break;

            default:
            case LabelAttr::DECOMPOSITION_FULL:
                $decompositionCzechPost = CzechPostApi::LABEL_DECOMPOSITION_FULL;
                break;
        }

        return $this->api->genetatePackageLabelByPackageId($id, null, CzechPostApi::LABEL_FORMAT_RAW, $decompositionCzechPost);
    }
}