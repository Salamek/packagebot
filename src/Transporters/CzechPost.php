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
use Salamek\PackageBot\Model\SendPackageResult;
use Salamek\PackageBot\Storage\ITransporterDataGroupStorage;
use Salamek\PackageBot\Storage\ITransporterDataItemStorage;

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
     * @param $tempDir
     * @param ITransporterDataGroupStorage $transporterDataGroupStorage
     * @param ITransporterDataItemStorage $transporterDataItemStorage
     */
    public function __construct(
        array $configuration, 
        array $sender,
        $tempDir,
        ITransporterDataGroupStorage $transporterDataGroupStorage,
        ITransporterDataItemStorage $transporterDataItemStorage
    )
    {

        $this->id = substr($configuration['senderId'], 1);
        $this->username = $configuration['username'];
        $this->password = $configuration['password'];

        $this->czechPostSender = new Sender($this->id, null, null, $sender['name'], $sender['www'], $sender['street'], $sender['streetNumber'], $sender['zipCode'], $sender['cityPart'], $sender['city'], $sender['country'], $configuration['postOfficeZipCode'], substr($configuration['senderId'], 0, 1));


        if (is_null($tempDir))
        {
            $cookieJar = tempnam(sys_get_temp_dir(), 'cookieJar.txt');
        }
        else
        {
            $cookieJar = $tempDir.'/cookieJar.txt';
        }

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
                $czechPostPaymentInfo = new PaymentInfo($package->getPaymentInfo()->getCashOnDeliveryPrice(), $package->getPaymentInfo()->getCashOnDeliveryCurrency(),
                    $package->getPaymentInfo()->getBankIdentifier());
            } else {
                $czechPostPaymentInfo = null;
            }

            if (!is_null($package->getWeightedPackageInfo())) {
                $czechPostWeighedPackageInfo = new WeightedPackageInfo($package->getWeightedPackageInfo()->getWeight(), $package->getWeightedPackageInfo()->getHeight(), $package->getWeightedPackageInfo()->getWidth(), $package->getWeightedPackageInfo()->getLength());
            } else {
                $czechPostWeighedPackageInfo = null;
            }

            return new TransporterPackage($package->getSeriesNumberInfo()->getSeriesNumber(), $deliveryType[$package->getTransportService()], $this->czechPostSender, $czechPostRecipient, $czechPostPaymentInfo, $czechPostWeighedPackageInfo, $package->getGoodsPrice(), [], $package->getDescription(), $package->getPackageCount(), $package->getPackagePosition(), ($package->getParentSeriesNumberInfo() ? $package->getParentSeriesNumberInfo()->getSeriesNumber() : null));
        } catch (WrongDataException $e) {
            throw new WrongDeliveryDataException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
    }

    /**
     * @param array $packages
     * @throws WrongDataException
     * @throws WrongDeliveryDataException
     * @return SendPackageResult[]
     */
    public function doSendPackages(array $packages)
    {
        $return = [];
        $transporterPackages = [];
        /** @var Package $package */
        foreach ($packages AS $package) {
            $transporterPackages[] = $this->packageBotPackageToTransporterPackage($package);
            //We gonna generate that right now, cos there is no way we can check status of CzechPost.createPackages... lets just assume everything went ok
            $return[] = new SendPackageResult(true, 0, 'OK', $package->getSeriesNumberInfo());
        }

        $this->api->createPackages($transporterPackages);

        return $return;
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

    /**
     * @param Package $package
     * @return string
     * @throws WrongDeliveryDataException
     */
    public function doGenerateTrackingUrl(Package $package)
    {
        $transporterPackage = $this->packageBotPackageToTransporterPackage($package);
        return 'https://www.postaonline.cz/trackandtrace/-/zasilka/cislo?parcelNumbers='.$transporterPackage->getPackageNumber();
    }

    /**
     * @param Package $package
     * @return string
     * @throws WrongDeliveryDataException
     */
    public function getPackageNumber(Package $package)
    {
        $transporterPackage = $this->packageBotPackageToTransporterPackage($package);
        return $transporterPackage->getPackageNumber();
    }

    /**
     * @return bool
     */
    public function hasLocalSeriesNumber()
    {
        return true;
    }

    public function doDataUpdate()
    {
        // TODO: Implement doDataUpdate() method.
    }
}