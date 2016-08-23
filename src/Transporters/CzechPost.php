<?php
namespace Salamek\PackageBot\Transporters;


use Salamek\CzechPostApi\Api;
use Salamek\CzechPostApi\Enum\Product;
use Salamek\CzechPostApi\Exception\WrongDataException;
use Salamek\CzechPostApi\Label;
use Salamek\CzechPostApi\Model\Package as TransporterPackage;
use Salamek\CzechPostApi\Model\PaymentInfo;
use Salamek\CzechPostApi\Model\Recipient;
use Salamek\CzechPostApi\Model\Sender;
use Salamek\CzechPostApi\Model\WeightedPackageInfo;
use Salamek\PackageBot\Enum\LabelPosition;
use Salamek\PackageBot\Enum\TransportService;
use Salamek\PackageBot\Exception\WrongDeliveryDataException;
use Salamek\PackageBot\Model\Package;

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
    
    /** @var Api */
    private $api;

    /** @var Sender */
    private $czechPostSender;

    /**
     * CzechPost constructor.
     * @param array $configuration
     * @param array $sender
     * @param $cookieJar
     */
    public function __construct(array $configuration, array $sender, $cookieJar)
    {
        $this->id = $configuration['senderId'];
        $this->username = $configuration['username'];
        $this->password = $configuration['password'];

        $this->czechPostSender = new Sender($this->id, null, null, $sender['name'], $sender['www'], $sender['street'], $sender['streetNumber'], $sender['zipCode'], $sender['cityPart'], $sender['city'], $sender['country'], $configuration['postOfficeZipCode']);

        $this->api = new Api($this->username, $this->password, $cookieJar);
    }

    /**
     * @param Package $package
     * @return TransporterPackage
     * @throws WrongDeliveryDataException
     */
    public function packageBotPackageToTransporterPackage(Package $package)
    {
        try {
            $deliveryType = [
                TransportService::CZECH_POST_PACKAGE_TO_HAND => Product::PACKAGE_TO_HAND,
                TransportService::CZECH_POST_PACKAGE_TO_THE_POST_OFFICE => Product::PACKAGE_TO_THE_POST_OFFICE
            ];

            $czechPostRecipient = new Recipient($package->getRecipient()->getFirstName(), $package->getRecipient()->getLastName(), $package->getRecipient()->getStreet(),
                $package->getRecipient()->getStreetNumber(), $package->getRecipient()->getCity(), $package->getRecipient()->getCityPart(), $package->getRecipient()->getZipCode(),
                $package->getRecipient()->getCompany(), $package->getRecipient()->getCompanyId(), $package->getRecipient()->getCompanyVatId(), $package->getRecipient()->getCountry(),
                $package->getRecipient()->getEmail(), $package->getRecipient()->getPhone(), $package->getRecipient()->getWww());

            if (!is_null($package->getPaymentInfo())) {
                $czechPostPaymentInfo = new PaymentInfo($package->getPaymentInfo()->getCashOnDeliveryCurrency(), $package->getPaymentInfo()->getCashOnDeliveryCurrency(),
                    $package->getPaymentInfo()->getBankIdentifier());
            } else {
                $czechPostPaymentInfo = null;
            }

            if (!is_null($package->getWeightedPackageInfo())) {
                $czechPostWeighedPackageInfo = new WeightedPackageInfo($package->getWeightedPackageInfo()->getWeight(), $package->getWeightedPackageInfo()->getHeight(), $package->getWeightedPackageInfo()->getWidth(), $package->getWeightedPackageInfo()->getLength());
            } else {
                $czechPostWeighedPackageInfo = null;
            }

            return new TransporterPackage($package->getSeriesNumberInfo()->getSeriesNumber(), $deliveryType[$package->getTransportService()], $this->czechPostSender, $czechPostRecipient, $czechPostPaymentInfo, $czechPostWeighedPackageInfo, $package->getGoodsPrice(), [], $package->getDescription(), $package->getPackageCount(), $package->getPackagePosition(), $package->getParentSeriesNumberInfo()->getSeriesNumber());
        } catch (WrongDataException $e) {
            throw new WrongDeliveryDataException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
    }

    /**
     * @param array $packages
     * @throws WrongDataException
     * @throws WrongDeliveryDataException
     * @return void
     */
    public function doSendPackages(array $packages)
    {
        $transporterPackages = [];
        /** @var Package $package */
        foreach ($packages AS $package) {
            $transporterPackages[] = $this->packageBotPackageToTransporterPackage($package);
        }

        $this->api->createPackages($transporterPackages);
    }

    /**
     * @param \TCPDF $pdf
     * @param Package $package
     * @return \TCPDF
     * @throws WrongDeliveryDataException
     */
    public function doGenerateLabelFull(\TCPDF $pdf, Package $package)
    {
        $transporterPackage = $this->packageBotPackageToTransporterPackage($package);
        return Label::generateLabelFull($pdf, $transporterPackage);
    }

    /**
     * @param \TCPDF $pdf
     * @param Package $package
     * @param int $position
     * @return \TCPDF
     * @throws WrongDeliveryDataException
     * @throws \Exception
     */
    public function doGenerateLabelQuarter(\TCPDF $pdf, Package $package, $position = LabelPosition::TOP_LEFT)
    {
        $transporterPackage = $this->packageBotPackageToTransporterPackage($package);
        return Label::generateLabelQuarter($pdf, $transporterPackage, $position);
    }
}