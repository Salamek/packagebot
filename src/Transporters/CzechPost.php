<?php
namespace Salamek\PackageBot\Transporters;

use Salamek\PackageBot\IPackageBotStorage;
use Salamek\PackageBot\PackageBot;
use Salamek\PackageBot\PackageBotPackage;
use Salamek\PackageBot\PackageBotParcelInfo;
use Salamek\PackageBot\PackageBotReceiver;
use Salamek\CzechPostApi;
use Salamek\CzechPostPackage;
use Salamek\CzechPostSender;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
class CzechPost implements ITransporter
{
    /** @var mixed  */
    private $id;

    /** @var mixed  */
    private $username;

    /** @var mixed  */
    private $password;

    /** @var IPackageBotStorage  */
    private $botStorage;

    /** @var CzechPostApi  */
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

        $czechPostSender = new CzechPostSender($this->id, $sender['name'], $sender['www'], $sender['street'], $sender['streetNumber'], $sender['zipCode'], $sender['cityPart'], $sender['city'], $configuration['postOfficeZipCode']);

        $this->api = new CzechPostApi($this->username, $this->password, $czechPostSender, new CzechPostStorage($this->botStorage), $cookieJar);
        
    }

    /**
     * @param PackageBotPackage $package
     * @param PackageBotReceiver $receiver
     * @return PackageBotParcelInfo
     */
    public function doParcel(PackageBotPackage $package, PackageBotReceiver $receiver)
    {
        $deliveryType = [
            PackageBotPackage::DELIVERY_TYPE_DELIVER => CzechPostPackage::DELIVERY_TYPE_DELIVER,
            PackageBotPackage::DELIVERY_TYPE_STORE => CzechPostPackage::DELIVERY_TYPE_STORE
        ];

        $czechPostPackage = new CzechPostPackage($receiver->getCompany(), $receiver->getFirstName(), $receiver->getLastName(), $receiver->getEmail(), $receiver->getPhone(), $receiver->getWww(), $receiver->getStreet(), $receiver->getStreetNumber(), $receiver->getZipCode(), $receiver->getCityPart(), $receiver->getCity(),
            $receiver->getState(), $package->getCashOnDeliveryPrice(), $package->getGoodsPrice(), [], $package->getBankIdentifier(), $package->getWeight(), $deliveryType[$package->getType()], $package->getDescription());

        $czechPostPackage->setWidth($package->getWidth());
        $czechPostPackage->setHeight($package->getHeight());
        $czechPostPackage->setLength($package->getLength());

        $this->api->persistPackage($czechPostPackage);

        $packageId = $this->api->generatePackageIdentifier($czechPostPackage);
        $label = $this->api->generatePackageLabel($czechPostPackage);

        $labelPath = $this->botStorage->savePackageLabel(PackageBot::TRANSPORTER_CZECH_POST, $packageId, $label);
        
        $packageBotParcelInfo = new PackageBotParcelInfo($packageId, $labelPath);

        return $packageBotParcelInfo;
    }

    public function doFlush()
    {
        $this->api->flushPackages();
    }
}